<?php
//header('Expires: 300s');
require '../../vendor/autoload.php';

use GeoIp2\Database\Reader;

$reader = new Reader('/var/www/default/GeoLite2-City_20190604/GeoLite2-City.mmdb');


$record = $reader->city($_SERVER['REMOTE_ADDR']);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <style>
    @import url('https://fonts.googleapis.com/css?family=IBM+Plex+Mono&display=swap');

    html { 
      background: url("18905.jpeg") no-repeat center center fixed; 
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }

    .label {
      color: #000000;
    }

    .disabled {
      /* color: #030303; */
    }

    .foreground, p, h1, h2, h3, h4, h5, h6, a:visited, a:link, a:active {
      color: #66CC66;
    }

    h3 {
      font-size: 150%;
    }

    a:hover {
      color: #99FF99;
      background-color: #006600;
    }

    .center {
      text-align: center;
    }

    .left {
      text-align: left;
    }

    .box {
      width: 33%;
      float: left;
      /* background-color: rgba(255,255,255,0.3); */
    }

    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    input {
      background-color: #003300;
      border: 1px #999999 solid;
      border-radius: 2px;
      color: #62727b;
      font-size: 120%;
    }
    input[type="search"] {
      width: 45%;
      border: none;
      border-bottom: 1px #006600 solid;
      text-align: center;
    }
    input[type="search"]:hover, input[type="search"]:focus {
      border-bottom-color: #003300;
    }

    ul {
      list-style-type: none;
    }

    a:visited, a:link, a:hover, a:active {
      text-decoration: none;
    }

    * {
      font-family: 'IBM Plex Mono', monospace;
      color: #33FF33;
      -moz-user-select: none;
    }

    body {
      background-color: rgba(0,0,0,0.6);
      padding-top: 30px;
      margin: 0;
    }

    #geo {
      position: absolute;
      top: 0px;
      right: 0px;
      margin-right: 0;
      padding-top: 10px;
      padding-right: 20px;
      text-align: right;
      height: 150px;
      width: 200px;
      background-color: rgba(255,255,255,0.1);
      border: 2px solid #999; 
    }
    
    #boxes {
      padding-bottom: 50px;
    }
    </style>
    <script>
    const categories = {
      "Anime": {
        "Crunchyroll": "https://crunchyroll.com",
        "KissAnime": "https://kissanime.ru",
        "Senpai Heat": "https://www.senpaiheat.com/"
      },
      "Gaming": {
        "GameJolt": "https://gamejolt.com/",
        "PlayOnLinux": "https://www.playonlinux.com/en/",
        "Steam": "https://store.steampowered.com/"
      },
      "Development": {
        "GitHub": "https://github.com",
        "Gitlab": "https://www.gitlab.com/",
        "Bitbucket": "https://bitbucket.com/",
        "Docker Hub": "https://hub.docker.com/",
      },
      "Linux": {
        "Debian": "https://www.debian.org/",
        "Ubuntu": "https://www.ubuntu.com/",
        "Armbian": "https://www.armbian.org/",
      },
      "Social": {
        "Reddit": "https://reddit.com",
        "Imgur": "https://imgur.com",
        "Twitter": "https://www.twitter.com/",
      },
      "Chat and Comms": {
        "Discord": "https://discordapp.com",
        "Imgur": "https://imgur.com",
	"Slack": "https://slack.com"
      },
      "Media": {
        "Netflix": "https://netflix.com",
        "Twitch": "https://twitch.tv",
	"YouTube": "https://youtube.com",
	"Invidio.us (YT alternative)": "https://invidio.us",
	"Amazon Prime": "https://www.amazon.co.uk/Prime-Video/b?ie=UTF8&node=3280626031&ref_=sd_allcat_k_fire_tv_piv"
      },
      "Servers": {
        "AWS": "https://aws.amazon.com",
        "Hetzner": "https://hetzner.com",
        "Vultr": "https://vultr.com",
        "Digital Ocean": "https://digitalocean.com",
        "Dreamhost": "https://panel.dreamhost.com",
	"Scaleway": "https://console.scaleway.com/dashboard",
	"Cloudflare": "https://cloudflare.com",
      },
      "Cloud": {
        "Wasabi": "https://wasabi.com",
        "Internet.bs": "https://internet.bs",
        "Namecheap": "https://namecheap.com",
	"123Reg": "https://123reg.co.uk",
	"Backblaze B2": "https://www.backblaze.com/b2/cloud-storage.html",
	"Mega": "https://mega.co.nz",
      },
      "Email and Docs": {
        "Gmail": "https://gmail.com",
	"GDrive": "https://drive.google.com"
      },
      "SSL": {
        "Common SSL commands": "https://www.sslshopper.com/article-most-common-openssl-commands.html",
	"SSL Labs Test": "https://www.ssllabs.com/ssltest/",
      },
      "Network Diag": {
        "Fast.com": "http://fast.com/",
	"Speedtest.net": "http://speedtest.net",
	"Lookingglass list": "http://www.bgplookingglass.com/",
      },
      "Search": {
        "StartPage": "https://www.startpage.com/",
	"DuckDuckGo": "https://duckduckgo/",
	"Gibiru": "https://gibiru.com/",
        "SwissCows": "https://swisscows.com/",
	"Yippy": "https://yippy.com/",
      }
    };

    const positions = ["left", "center", "right"];

    const removeAllChildren = (elem) => {
      while (elem.firstChild) {
        removeAllChildren(elem.firstChild);
        elem.removeChild(elem.firstChild);
      }
    };
    const buildEntry = (name, url) => {
      let root_elem = document.createElement("a");
      root_elem.innerText = name;
      root_elem.href = url;
      return root_elem;
    };
    const buildRow = (sites, categoryName, pos) => {
      let root_elem = document.createElement("div");
      root_elem.classList.add(pos);
      root_elem.classList.add("box");

      let label_elem = document.createElement("h3");
      label_elem.classList.add("center");
      label_elem.innerText = categoryName;
      root_elem.appendChild(label_elem);

      let list_elem = document.createElement("ul");
      list_elem.classList.add("links");
      list_elem.classList.add("left");
      root_elem.appendChild(list_elem);

      for (const siteName of Object.keys(sites).sort((a, b) => a.localeCompare(b))) {
        const site = sites[siteName];
        const item_elem = document.createElement("li");
        item_elem.appendChild(buildEntry(siteName, site));
        list_elem.appendChild(item_elem);
      }
      return root_elem;
    };
    const buildBoxes = (query="") => {
      let boxes_elem = document.getElementById("boxes");
      let cats = {};
      for (const catName of Object.keys(categories).sort((a, b) => a.localeCompare(b))) {
        for (const siteName in categories[catName]) {
          if (siteName.toLowerCase().includes(query.toLowerCase())) {
            if (typeof cats[catName] === "undefined") {
              cats[catName] = {};
            }
            cats[catName][siteName] = categories[catName][siteName];
          }
        }
      }
      removeAllChildren(boxes_elem);
      let index = 0;
      let rows = [];
      for (const categoryName in cats) {
        if (Object.keys(cats[categoryName]).length === 0) {
          continue;
        }
        if (index % 3 === 0) {
          let row_elem = document.createElement("div");
          row_elem.classList.add("row");
          rows.push(row_elem);
        }
        rows[rows.length-1].appendChild(buildRow(cats[categoryName], categoryName, positions[index % 3]));
        index++;
      }
      for (const row of rows) {
        row.appendChild(document.createElement("br"));
        boxes_elem.appendChild(row);
      }
    };
    window.onload = () => {
      buildBoxes();
      let search_elem = document.getElementById("search");
      search_elem.oninput = () => buildBoxes(search_elem.value);
      search_elem.onsubmit = (ev) => ev.preventDefault();
    };
    </script>
    <title>Start Page</title>
  </head>
  <body>
  <div id="geo"><pre>
<?php
print($_SERVER['REMOTE_ADDR'] . "\n");
print($record->country->isoCode . "\n"); // 'US'
print($record->country->name . "\n"); // 'United States'
print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
print($record->city->name . "\n"); // 'Minneapolis'


print($record->location->latitude . ", "); // 44.9733
print($record->location->longitude . "\n"); // -93.2323

?>
</pre></div>
    <div class="label center">
      <h1>Start Page</h1>
      <iframe src="https://duckduckgo.com/search.html?prefill=Search DDG&bgcolor=000000&focus=yes" style="overflow:hidden;margin:0;padding:0;width:408px;height:40px;" frameborder="0"></iframe>
    </div>
    <div id="boxes">
    </div>
  </body>
</html>
