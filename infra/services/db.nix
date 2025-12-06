{
  config,
  lib,
  pkgs,
  dbName,
  dbUsername,
  dbPassword,
  ...
}:

let

  mongodb =
    (import <nixpkgs> {
      config.permittedInsecurePackages = [
      ];
      config.allowUnfreePredicate =
        pkg:
        builtins.elem (lib.getName pkg) [
          "mongodb-ce"
        ];
    }).mongodb-ce;

  initialScript = pkgs.writeTextFile {
    name = "mongodb-init.js";
    text = ''
      use admin;

      db.createUser(
        {
          user: '${dbUsername}',
          pwd: '${dbPassword}',
          roles:  [
            {
              role: 'readWrite',
              db: '${dbName}'
            }
          ]
        }
      );
    '';
  };

in
{

  services.mongodb.enable = true;
  services.mongodb.package = mongodb;
  services.mongodb.initialScript = initialScript;

}
