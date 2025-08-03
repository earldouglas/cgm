{ pkgs, lib, config, inputs, ... }:

let

  nodejs = pkgs.nodejs_22;

  nightscout = import ./frontend/nightscout.nix {
    inherit pkgs nodejs;
  };

in {

  # https://devenv.sh/basics/
  # env.GREET = "devenv";

  # https://devenv.sh/packages/
  packages = [
    pkgs.sbt
  ];

  # https://devenv.sh/languages/
  # languages.rust.enable = true;

  # https://devenv.sh/processes/
  # processes.cargo-watch.exec = "cargo-watch";
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
      server_name  localhost;

      location /api/v4 {
        proxy_pass http://localhost:8080;
      }

      location / {
        proxy_pass http://localhost:1337;
      }
    }
  '';

  # https://devenv.sh/scripts/
  # scripts.hello.exec = ''
  #   echo hello from $GREET
  # '';

  # enterShell = ''
  #   hello
  #   git --version
  # '';

  # https://devenv.sh/tasks/
  # tasks = {
  #   "myproj:setup".exec = "mytool build";
  #   "devenv:enterShell".after = [ "myproj:setup" ];
  # };

  # https://devenv.sh/tests/
  # enterTest = ''
  #   echo "Running tests"
  #   git --version | grep --color=auto "${pkgs.git.version}"
  # '';

  # https://devenv.sh/git-hooks/
  # git-hooks.hooks.shellcheck.enable = true;

  # See full reference at https://devenv.sh/reference/options/

}
