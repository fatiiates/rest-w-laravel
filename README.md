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
    
Eğer Docker başarılı bir şekilde kurulduysa, hello-world konteynırımızı çalıştırarak test edelim.

    sudo docker run hello-world

Kurulum tamamlandı.

# Uygulamayı Çalıştırmak

Uygulama 80 ve 3306 portlarını kullanmaktadır. Bilgisayarınızıda bu portları varsayılan olarak kullananan servisleri aşağıdaki komutları kullanarak kapatabilirsiniz.

    sudo apache2ctl stop
    sudo service mysql stop

İlk olarak laravelin gerektirdiği paketleri ve composer.lock dosyasını oluşturalım.

    docker run --rm -v $(pwd):/app composer install

Aşağıdaki komut ile konteynırlarımızı derleyerek ayağa kaldıralım.

    docker-compose up -d --build
    
Daha sonra uygulamamız için yeni bir APP_KEY oluşturalım ve konfigürasyonlarımızı cache.php içerisine aktaralım.

    docker-compose exec pynar-app php artisan key:generate
    docker-compose exec pynar-app php artisan config:cache
    
Önbellekleme tamamlandıktan sonra veritabanında 'laravel' isminde veritabanımız oluşmuş olmalıdır. Veritabanı konteynırımızın bash arayüzüne girdikten sonra mysql komutları ile bunu doğrulayalım

    docker-compose exec pynar-db bash
    
Bash içerisinde kullanılacak komutlar

    mysql -u -root -p
    
Mysql içerisinde kullanılacak komutlar

    SHOW DATABASES;
    
Sonuç şuna benzemelidir.

    +--------------------+
    | Database           |
    +--------------------+
    | information_schema |
    | laravel            |
    | mysql              |
    | performance_schema |
    | sys                |
    +--------------------+
    5 rows in set (0.00 sec)
  
Eğer ki sonuçlarınız uyuşuyorsa kullanıcı oluşturmaya geçebiliriz. Yeni bir kullanıcı oluşturalım ve 'laravel' veritabanı üzerindeki tüm yetkileri kendisine verelim.
    
    CREATE USER 'laraveluser'@'%' IDENTIFIED WITH mysql_native_password BY 'sizin_env_içinde_tanımladığınız_şifreniz';
    GRANT ALL ON laravel.* TO 'laraveluser'@'%';
    FLUSH PRIVILEGES;
    
Başarıyla kullanıcı oluşturulduktan sonra veritabanımızı eşleştirelim.(Aşağıdaki komut daha önceki verilerinizi siler ve tamamiyle veritabanınızı yeniler)

    docker-compose exec pynar-app php artisan migrate:refresh
    
Yeni eşleştirme yaptıktan sonra kullanıcı güvenliği ve login sistemi için aşağıdaki komutu çalıştıralım.

    docker-compose exec pynar-app php artisan passport:install
    
Uygulama başarıyla çalıştırıldı.
