# 🔌 Model Context Protocol (MCP) Configuration

This directory and [`.mcp.json`](../.mcp.json) manage Model Context Protocol (MCP) servers and tools for **Lara-CMS**.

---

## 🛠️ Active MCP Servers (`.mcp.json`)

1. **`laravel-boost`**
   - **Command**: `php artisan boost:mcp`
   - **Purpose**: Provides Laravel-native schema inspection, database query execution, browser logs, and version-specific Laravel documentation search.

2. **`TestSprite`**
   - **Command**: `npx -y @testsprite/testsprite-mcp@latest`
   - **Purpose**: Automated end-to-end frontend testing and verification.

---

## 📁 Custom MCP Server Directory (`.mcp/servers/`)

If you want to add custom local MCP servers (Node.js, Python, or PHP):
1. Place your server entry script in `.mcp/servers/<server-name>/index.js` (or script).
2. Register the server in `.mcp.json` under `"mcpServers"`.
