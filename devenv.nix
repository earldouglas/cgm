{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

let

  nodejs = pkgs.nodejs_22;

  nightscout = import ./frontend/nightscout.nix {
    inherit pkgs nodejs;
  };

in
{

  # https://devenv.sh/languages/
  languages.php = {
    enable = true;
    version = "8.4";
    extensions = [
      "curl"
      "mongodb"
    ];

    ini = ''
      memory_limit = 512M
    '';

    fpm.pools.web = {
      settings = {
        "pm" = "dynamic";
        "pm.max_children" = 5;
        "pm.start_servers" = 2;
        "pm.min_spare_servers" = 1;
        "pm.max_spare_servers" = 5;
        "security.limit_extensions" = ".php";
      };
    };
  };

  # https://devenv.sh/processes/
  processes.nightscout.exec = ''
    export BASE_URL="http://localhost:8888"
    export MONGODB_URI="mongodb://localhost:27017/cgm"
    export API_SECRET=1234567890abc
    export HOSTNAME=localhost
    export INSECURE_USE_HTTP=true
    export PORT=1337
    cd ${nightscout}/lib/node_modules/nightscout
    ${nodejs}/bin/node lib/server/server.js
  '';

  # https://devenv.sh/services/
  services.mongodb.enable = true;
  services.nginx.enable = true;
  services.nginx.httpConfig = ''
    server {
      listen       8888;
      server_name  _;

      location ~ ^/api/v4/.+\.php$ {
        root ${config.env.DEVENV_ROOT}/backend;
        include ${pkgs.nginx}/conf/fastcgi.conf;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:${config.languages.php.fpm.pools.web.socket};
      }

      location /api/v4 {
        try_files $uri $uri/ $uri.php$is_args$query_string;
      }

      location / {
        proxy_pass http://localhost:1337;
      }
    }
  '';

  enterTest = ''
    wait_for_port 8888
    export TEST=true
    find backend/ -type f -name "*.php" -print0 | \
      xargs -n 1 -0 php
  '';
}
