{
  config,
  lib,
  pkgs,
  apiSecret,
  nightscoutPort,
  dbName,
  dbUsername,
  dbPassword,
  ...
}:

let

  nodejs = pkgs.nodejs_22;

  nightscout = pkgs.buildNpmPackage (finalAttrs: {

    nativeBuildInputs = [ pkgs.webpack-cli ];

    nodejs = nodejs;

    name = "cgm-remote-monitor";

    src = pkgs.fetchFromGitHub {
      owner = "earldouglas";
      repo = "cgm-remote-monitor";
      rev = "1822278ce5a91a0c4eac1987956877e4aeb34ef4";
      hash = "sha256-5Y0N5XY2yjkerZu/2aldqOSzgplHcCvPh1a47kuna7c=";
    };

    npmDepsHash = "sha256-9RslybJ6c/JxmD3UPOba1oVYfxirg52Lp8kiv93TVsI=";

    npmInstallFlags = [ "--only=production" ];

    npmBuildScript = "bundle";

  });

in
{

  ## nightscout service account ########################################

  users.groups.nightscout = { };
  users.users.nightscout = {
    group = "nightscout";
    isSystemUser = true;
  };

  ## nightscout service ################################################

  systemd.services.nightscout = {
    description = "nightscout";
    after = [ "network.target" ];
    wantedBy = [ "multi-user.target" ];
    serviceConfig = {
      WorkingDirectory = "${nightscout}/lib/node_modules/nightscout";
      ExecStart = "${nodejs}/bin/node lib/server/server.js";
      User = "nightscout";
      Restart = "always";
    };
    environment = {

      ## Required ######################################################
      #
      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#required

      MONGODB_URI = "mongodb://${dbUsername}:${dbPassword}@localhost:27017/${dbName}";
      API_SECRET = apiSecret;
      MONGODB_COLLECTION = "entries";
      DISPLAY_UNITS = "mg/dl";

      ## Features ######################################################
      #
      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#features

      ENABLE = lib.strings.concatStrings (
        lib.intersperse " " [

          # "basal"
          # "boluscalc"
          # "careportal"
          # "connect" # get BG from Dexcom
          # "devicestatus"
          # "food"

          "ar2" # alarms based on forecasted values
          "delta" # show the change between the last 2 BG values
          "direction" # show the trend direction
          "errorcodes" # alarms for CGM codes 9 (hourglass) and 10 (???)
          "profile" # profile editor
          "simplealarms" # generate alarms for high, top, bottom, low
          "speech" # speaks out the blood glucose values, IOB and alarms
          "timeago" # show time since last CGM entry
          "wake-lock" # show wake lock toggle button

        ]
      );

      LANGUAGE = "en";

      AUTH_DEFAULT_ROLES = "denied";

      BASE_URL = "https://${config.networking.hostName}.${config.networking.domain}";

      ## Core ##########################################################
      #
      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#core

      PORT = toString nightscoutPort;

      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#predefined-values-for-your-server-settings-optional

      TIME_FORMAT = "24";
      INSECURE_USE_HTTP = "true";
      THEME = "colors";

      ## Dexcom Share ##################################################
      #
      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#connect-nightscout-connect

      #CONNECT_SOURCE = "dexcomshare";
      #CONNECT_SHARE_ACCOUNT_NAME = dexcomUsername;
      #CONNECT_SHARE_PASSWORD = dexcomPassword;
      #CONNECT_SHARE_REGION = "ous";

      ## Glooko ########################################################
      #
      # See https://github.com/nightscout/nightscout-connect?tab=readme-ov-file#glooko

      # CONNECT_SOURCE = "glooko";
      # CONNECT_GLOOKO_EMAIL = glookoEmail;
      # CONNECT_GLOOKO_PASSWORD = glookoPassword;
      # CONNECT_GLOOKO_TIMEZONE_OFFSET = "-7";

      ## Plugins #######################################################
      #
      # See https://github.com/nightscout/cgm-remote-monitor?tab=readme-ov-file#plugins

      AR2_CONE_FACTOR = "0";
    };
  };

}
