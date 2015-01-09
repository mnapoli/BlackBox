VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    config.vm.hostname = "piwik"
    config.vm.box = "trusty64"

    config.vm.provision :shell, path: "vagrant/bootstrap.sh"

end
