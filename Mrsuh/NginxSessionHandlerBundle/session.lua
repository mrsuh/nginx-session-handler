local redis = require "resty.redis" -- include redis lib
local red = redis:new()

red:set_timeout(1000) -- 1 sec

local ok = red:connect("127.0.0.1", 6379) -- connect to redis
if not ok then
    ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
end

local phpsession = ngx.var.cookie_PHPSESSID -- get session id from cookie
local ROLE_ADMIN = "ROLE_ADMIN"  -- role name

if phpsession == ngx.null then
    ngx.exit(ngx.HTTP_FORBIDDEN)
end

local res = red:hget("phpsession:" .. phpsession, "user-role") --get role from redis by session id

if res == ngx.null or res ~= ROLE_ADMIN then -- if role is wrong or session not found return code 403
    ngx.exit(ngx.HTTP_FORBIDDEN)
end