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

  api-secret = "1234567890abc";
  api-secret-sha1 = builtins.hashString "sha1" api-secret;

  mongodb-root = "mongodb://localhost:27017";
  mongodb-name = "cgm";

in
{

  # https://devenv.sh/basics/
  env.API_SECRET = api-secret;
  env.API_SECRET_SHA1 = api-secret-sha1;

  # https://devenv.sh/packages/
  packages = [
    pkgs.curl
    pkgs.jq
    pkgs.time
  ];

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
      phpEnv = {
        "API_SECRET" = api-secret;
        "MONGODB_ROOT" = mongodb-root;
        "MONGODB_NAME" = mongodb-name;
      };
    };
  };

  # https://devenv.sh/processes/
  processes.nightscout.exec = ''
    export BASE_URL=http://localhost:8888
    export MONGODB_URI=${mongodb-root}/${mongodb-name}
    export API_SECRET=${api-secret}
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

    echo 'Waiting for backend...'
    curl -s 'http://localhost:8888/api/v1/status' \
      --retry 30 \
      --retry-delay 1 \
      --retry-connrefused \
      -H 'api-secret: ${api-secret-sha1}' > /dev/null 2> /dev/null

    function test() {

      echo "Running tests..."

      PASS=true

      for i in `find backend/ -type f -name "*.php"`
      do
        export MONGODB_ROOT=${mongodb-root}
        export MONGODB_NAME=${mongodb-name}
        php "''${i}" || PASS=false
      done

      if [ "''${PASS}" = "true" ]
      then
        echo -e "\033[32m✔\033[0m All tests passed!"
      else
        echo -e "\033[31m✖\033[0m Some tests failed!"
        return -1
      fi
    }

    function wait() {
      ${pkgs.inotifyTools}/bin/inotifywait \
        --exclude '(\.devenv.*)|(devenv.local.nix)|(\.git/.*)' \
        -e modify \
        -e close_write \
        -e moved_to \
        -e moved_from \
        -e move \
        -e move_self \
        -e create \
        -e delete \
        -e delete_self \
        -e unmount \
        -r \
        .
    }

    function watch() {
      while [ TRUE ]
      do
        time test || true
        echo "Watching for changes..."
        wait
      done
    }

    if [ "''${WATCH:-}" = "" ]
    then
      test
    else
      watch
    fi
  '';

  processes.hydrate = {
    exec = ''

      echo 'Waiting for backend'
      curl -s 'http://localhost:8888/api/v1/status' \
        --retry 30 \
        --retry-delay 1 \
        --retry-connrefused \
        -H 'api-secret: ${api-secret-sha1}' > /dev/null 2> /dev/null

      echo 'Setting test profile'
      curl -s 'http://localhost:8888/api/v1/profile' \
        -X PUT \
        -H 'Content-Type: application/json' \
        -H 'api-secret: ${api-secret-sha1}' \
        --data @./test/profile.json

    '';
  };

}
