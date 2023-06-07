-- UBUNTU 18.04 --

cd /home
wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta.sh
chmod 777 ./installer-beta.sh && sed -i -e 's/\r$//' installer-beta.sh && ./installer-beta.sh

-- UBUNTU 20.04 --

cd /home
wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta_2004.sh
chmod 777 ./installer-beta_2004.sh && sed -i -e 's/\r$//' installer-beta_2004.sh && ./installer-beta_2004.sh
