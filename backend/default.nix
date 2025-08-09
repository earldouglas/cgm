let

  pkgs =

    let

      pkgs = import <nixpkgs> { };

      sbt-derivation-src = pkgs.fetchFromGitHub {
        owner = "zaninime";
        repo = "sbt-derivation";
        rev = "6762cf2c31de50efd9ff905cbcc87239995a4ef9";
        sha256 = "sha256-Pnej7WZIPomYWg8f/CZ65sfW85IfIUjYhphMMg7/LT0=";
      };

      sbt-derivation = import "${sbt-derivation-src}/overlay.nix";

      sbt-overlay = final: prev: {
        sbt = prev.sbt.override {
          jre = prev.jdk21;
        };
      };

    in

    import <nixpkgs> {
      overlays = [
        sbt-derivation
        sbt-overlay
      ];
    };

  buildCmd = "sbt package";

in

pkgs.mkSbtDerivation {

  pname = "backend";
  version = "0.1.0-SNAPSHOT";

  depsSha256 = "sha256-MIwPO3zkSIanDE7RxNFB48Q1Mo0s6Q/9JySj/tWUgJc=";

  src = ./.;

  depsWarmupCommand = buildCmd;

  buildPhase = buildCmd;

  installPhase = ''
    mkdir -p $out/
    find target/
    cp target/scala-*/backend*.war $out/backend.war
  '';

  meta = {
    description = "Cloud Glucose Monitor";
    homepage = "https://github.com/earldouglas/cloud-glucose-monitor";
    license = pkgs.lib.licenses.isc;
    maintainers = [ pkgs.lib.maintainers.earldouglas ];
  };
}
