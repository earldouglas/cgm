{ pkgs, nodejs }:

pkgs.buildNpmPackage (finalAttrs: {

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

})
