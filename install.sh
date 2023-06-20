#!/bin/bash

os_version=$(lsb_release -rs)

if [[ $os_version == "18.04" ]]; then
    echo "Detected Ubuntu 18.04"
    cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta.sh && chmod 777 ./installer-beta.sh && sed -i -e 's/\r$//' installer-beta.sh && ./installer-beta.sh
elif [[ $os_version == "20.04" ]]; then
    echo "Detected Ubuntu 20.04"
    cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta_2004.sh && chmod 777 ./installer-beta_2004.sh && sed -i -e 's/\r$//' installer-beta_2004.sh && ./installer-beta_2004.sh
elif [[ $os_version == "22.04" ]]; then
    echo "Detected Ubuntu 22.04"
    cd /home && wget https://raw.githubusercontent.com/DRM-Scripts/DRMPHP/master/installer-beta_2204.sh && chmod 777 ./installer-beta_2204.sh && sed -i -e 's/\r$//' installer-beta_2204.sh && ./installer-beta_2204.sh
else
    echo "Unsupported Ubuntu version: $os_version"
    echo "Please choose one of the following supported versions:"
    echo "18.04, 20.04, 22.04"
    exit 1
fi
