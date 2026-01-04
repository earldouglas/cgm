{
  tconnectEmail,
  tconnectPassword,
  tconnectRegion,
  nightscoutPort,
  nightscoutSecret,
  timezone,
  pumpSerialNumber,
  ...
}:

{

  # allow <podman guest> -> <podman host>:<nightscout port>
  networking.firewall.extraCommands = ''
    iptables -A nixos-fw -p tcp --source 10.88.0.0/16 --dport ${toString nightscoutPort}:${toString nightscoutPort} -j nixos-fw-accept
'';

  # service ############################################################

  virtualisation.oci-containers.containers = {
    tconnectsync = {
      environment = {
        TCONNECT_EMAIL = tconnectEmail;
        TCONNECT_PASSWORD = tconnectPassword;
        TCONNECT_REGION = tconnectRegion;
        NS_URL = "http://host.containers.internal:${toString nightscoutPort}/";
        NS_SECRET = nightscoutSecret;
        TIMEZONE_NAME = timezone;
        PUMP_SERIAL_NUMBER = pumpSerialNumber;
      };
      image = "jwoglom/tconnectsync:2.3.4";
      cmd = [
        "--auto-update"
      ];
    };
  };

}
