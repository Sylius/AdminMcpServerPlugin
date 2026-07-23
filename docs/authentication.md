# Authentication Flow (OAuth 2.0 PKCE)

The plugin implements OAuth 2.0 Authorization Code flow with PKCE. Below is the complete flow.

## Step 1 — Register an OAuth client

```bash
curl -X POST "https://your-domain.com/_mcp/oauth/register" \
  -H "Content-Type: application/json" \
  -d '{
    "client_name": "My MCP Client",
    "redirect_uris": ["http://localhost:3000/callback"],
    "grant_types": ["authorization_code"],
    "token_endpoint_auth_method": "none"
  }'
```

Response:
```json
{
  "client_id": "7c668d1d25b54fa65bc2bdc5a31f8b7e5701034c",
  "client_id_issued_at": 1784722394,
  "redirect_uris": ["http://localhost:3000/callback"],
  "grant_types": ["authorization_code"],
  "token_endpoint_auth_method": "none",
  "client_name": "My MCP Client"
}
```

## Step 2 — Generate PKCE code verifier and challenge

```python
import os, base64, hashlib
verifier = base64.urlsafe_b64encode(os.urandom(32)).rstrip(b'=').decode()
challenge = base64.urlsafe_b64encode(hashlib.sha256(verifier.encode()).digest()).rstrip(b'=').decode()
print(f"Verifier: {verifier}")
print(f"Challenge: {challenge}")
```

## Step 3 — Redirect user to authorization page

```
GET /admin/mcp/oauth/authorize
  ?response_type=code
  &client_id=YOUR_CLIENT_ID
  &redirect_uri=YOUR_REDIRECT_URI
  &code_challenge=YOUR_CHALLENGE
  &code_challenge_method=S256
  &state=RANDOM_STATE
```

The user must be logged in as an admin with `ROLE_API_ACCESS`. They will see a consent page and can approve or deny the request.

## Step 4 — Exchange authorization code for access token

After approval the user is redirected to your `redirect_uri` with a `code` parameter. Exchange it:

```bash
curl -X POST "https://your-domain.com/_mcp/oauth/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=authorization_code" \
  -d "client_id=YOUR_CLIENT_ID" \
  -d "redirect_uri=YOUR_REDIRECT_URI" \
  -d "code=THE_AUTH_CODE" \
  -d "code_verifier=YOUR_VERIFIER"
```

Response:
```json
{
  "token_type": "Bearer",
  "expires_in": 3600,
  "access_token": "eyJ0eXAiOi...",
  "refresh_token": "def50200..."
}
```

## Step 5 — Use the access token with MCP

```bash
# Initialize MCP session
SESSION_ID=$(curl -s -D - -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1"}}}' \
  | grep -i "mcp-session-id:" | tr -d '\r' | awk '{print $2}')

# Call an MCP tool
curl -X POST "https://your-domain.com/_mcp" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Mcp-Session-Id: $SESSION_ID" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/call","params":{"name":"list_channels","arguments":{}}}'
```

## Refresh Token

Access tokens expire after 1 hour. Use the refresh token to obtain a new one without re-authorization:

```bash
curl -X POST "https://your-domain.com/_mcp/oauth/token" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=refresh_token" \
  -d "client_id=YOUR_CLIENT_ID" \
  -d "refresh_token=YOUR_REFRESH_TOKEN"
```

## MCP Client Configuration (Claude Desktop / Cursor)

The plugin follows the MCP HTTP transport specification with OAuth 2.0 PKCE. Discovery endpoints enable automatic client configuration:

- `/.well-known/oauth-authorization-server` — OAuth server metadata
- `/.well-known/oauth-protected-resource` — Resource server metadata
