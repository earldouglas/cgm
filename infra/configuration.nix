{
  config,
  lib,
  pkgs,
  modulesPath,
  ...
}:

let

  getEnv =
    name:
    let
      value = builtins.getEnv name;
    in
    if builtins.stringLength value == 0 then throw "${name} env var is required" else value;

  apiSecret = getEnv "API_SECRET";

  nightscoutPort = 9000;

  hostName = getEnv "HOST_NAME";
  domain = getEnv "DOMAIN";

  system = import ./services/system.nix {
    inherit modulesPath;
    inherit hostName domain;
  };

  db = import ./services/db.nix {
    inherit config lib pkgs;
    dbName = getEnv "MONGO_DB_NAME";
    dbUsername = getEnv "MONGO_DB_USERNAME";
    dbPassword = getEnv "MONGO_DB_PASSWORD";
  };

  frontend = import ./services/frontend.nix {
    inherit config lib pkgs;
    inherit apiSecret nightscoutPort;
  };

  backend = import ./services/backend.nix {
    inherit config pkgs;
    inherit hostName domain apiSecret;
  };

  gateway = import ./services/gateway.nix {
    inherit config pkgs;
    inherit hostName domain nightscoutPort;
  };

in
{

  imports = [
    system
    db
    frontend
    backend
    gateway
  ];

}
