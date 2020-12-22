# Docker Kurulumu

## Ubuntu 20.04 LTS
    
Daha önce bilgisayarınızda kuruluysa öncelikle o paketleri kaldıralım.
    
    sudo apt-get remove docker docker-engine docker.io containerd runc
    
Apt ile HTTPS üzerinden indirme yapmak için paketleri ekleyin.

    sudo apt-get update

    sudo apt-get install \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg-agent \
        software-properties-common
        
        
Docker GPG keyini ekleyelim.

    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
    
Apt-keylerimize Docker'ın fingerprint keyini ekleyelim.

    sudo apt-key fingerprint 0EBFCD88
    
Docker repository adresini makinemize dahil ederek devam ediyoruz.

    sudo add-apt-repository \
       "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"
   
Artık docker paketlerini kurabiliriz.

    sudo apt-get update
    sudo apt-get install docker-ce docker-ce-cli containerd.io
    
Eğer Docker başarılı bir şekilde kurulduysa, hello-world containerımızı çalıştırarak test edelim.

    sudo docker run hello-world

Kurulum tamamlandı.
