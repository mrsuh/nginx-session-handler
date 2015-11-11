local redis = require "resty.redis"
local red = redis:new()

red:set_timeout(1000) -- 1 sec

local ok = red:connect("127.0.0.1", 6379)
if not ok then
    ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
end

local phpsession = ngx.var.cookie_PHPSESSID
local ROLE_ADMIN = "ROLE_ADMIN"

if phpsession == ngx.null then
    ngx.exit(ngx.HTTP_FORBIDDEN)
end

local res = red:hget("phpsession:" .. phpsession, "user-role")

if res == ngx.null or res ~= ROLE_ADMIN then
    ngx.exit(ngx.HTTP_FORBIDDEN)
end