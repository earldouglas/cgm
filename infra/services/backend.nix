{
  config,
  pkgs,
  hostName,
  domain,
  apiSecret,
  dbName,
  dbUsername,
  dbPassword,
  ...
}:

{

  # php-fpm service account ############################################

  users.groups.cgm = { };
  users.users.cgm = {
    group = "cgm";
    isSystemUser = true;
  };
  users.users.nginx.extraGroups = [ "cgm" ];

  # php-fpm ############################################################

  services.phpfpm.pools.cgm = {
    user = "cgm";
    settings = {
      "pm" = "dynamic";
      "pm.max_children" = 5;
      "pm.start_servers" = 2;
      "pm.min_spare_servers" = 1;
      "pm.max_spare_servers" = 5;
      "security.limit_extensions" = ".php";
      "listen.owner" = config.services.nginx.user;
      "listen.group" = config.services.nginx.group;
      "listen.mode" = "0660";
      "catch_workers_output" = 1;
    };
    phpEnv = {
      "API_SECRET" = apiSecret;
      "MONGODB_ROOT" = "mongodb://${dbUsername}:${dbPassword}@localhost:27017";
      "MONGODB_NAME" = dbName;
    };
    phpOptions = ''
      memory_limit = 512M
    '';
    phpPackage = pkgs.php.buildEnv {
      extensions =
        { enabled, all }:
        enabled
        ++ [
          all.curl
          all.mongodb
        ];
    };
  };

  services.nginx.virtualHosts."${hostName}.${domain}" = {
    locations."~ ^/api/v4/.+\\.php$".extraConfig = ''
      root ${../../backend/src};
      include ${pkgs.nginx}/conf/fastcgi.conf;
      fastcgi_split_path_info ^(.+\.php)(/.+)$;
      fastcgi_pass unix:${config.services.phpfpm.pools.cgm.socket};
    '';
    locations."/api/v4".extraConfig = ''
      try_files $uri $uri/ $uri.php$is_args$query_string;
    '';
  };

}
