{
  config,
  pkgs,
  hostName,
  domain,
  nightscoutPort,
  acmeEmail,
  ...
}:

{

  networking.firewall.allowedTCPPorts = [ 80 443 ];

  security.acme.defaults.email = acmeEmail;
  security.acme.acceptTerms = true;

  services.nginx.enable = true;
  services.nginx.recommendedOptimisation = true;
  services.nginx.recommendedGzipSettings = true;
  services.nginx.recommendedProxySettings = true;
  services.nginx.commonHttpConfig = ''
    charset utf-8;
    log_format postdata '$time_local\t$remote_addr\t$request_body';
    limit_req_zone $binary_remote_addr zone=ip:10m rate=16r/s;
    add_header Permissions-Policy "interest-cohort=()";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
  '';

  services.nginx.virtualHosts."${hostName}.${domain}" = {
    enableACME = true;
    forceSSL = true;
    locations."/".extraConfig = ''
      proxy_pass http://localhost:${toString nightscoutPort};
    '';
  };

}
