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

  dbName = getEnv "MONGODB_NAME";
  dbUsername = getEnv "MONGODB_USERNAME";
  dbPassword = getEnv "MONGODB_PASSWORD";

  system = import ./services/system.nix {
    inherit modulesPath;
    inherit hostName domain;
  };

  db = import ./services/db.nix {
    inherit config lib pkgs;
    inherit dbName dbUsername dbPassword;
  };

  frontend = import ./services/frontend.nix {
    inherit config lib pkgs;
    inherit apiSecret nightscoutPort dbName dbUsername dbPassword;
  };

  backend = import ./services/backend.nix {
    inherit config pkgs;
    inherit hostName domain apiSecret dbName dbUsername dbPassword;
  };

  tconnectsync = import ./services/tconnectsync.nix {
    inherit nightscoutPort;
    tconnectEmail = getEnv "TCONNECT_EMAIL";
    tconnectPassword = getEnv "TCONNECT_PASSWORD";
    tconnectRegion = getEnv "TCONNECT_REGION";
    nightscoutSecret = getEnv "TCONNECT_NS_SECRET";
    timezone = getEnv "TCONNECT_TIMEZONE_NAME";
    pumpSerialNumber = getEnv "TCONNECT_PUMP_SERIAL_NUMBER";
  };

  gateway = import ./services/gateway.nix {
    inherit config pkgs;
    inherit hostName domain nightscoutPort;
    acmeEmail = getEnv "ACME_EMAIL";
  };

in
{

  imports = [
    system
    db
    frontend
    backend
    tconnectsync
    gateway
  ];

}
