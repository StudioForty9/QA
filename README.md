# StudioForty9 Quality Assurance module

##Features
The module provides standard Magento e-commerce Behat tests that should pass on a vanilla installation of Magento and continue to pass provided standard StudioForty9 development practices are followed.

It also allows for a straightforward mechanism to extend these tests to take customisations into account and to add additional tests for custom features.

Finally, it integrates the ECG PHP Code Sniffer and PHP Mess Detector standards into a PHPStorm project without any developer intervention.

##Composer Installation

Add the repository to your project `composer.json` file:
	
	"repositories": [
    	{
    	 	 "type": "vcs",
	    	  "url": "http://github.com/studioforty9/qa"
	    },
	    {
		      "url": "git@github.com:alexandresalome/mailcatcher.git",
    		  "type": "git"
	    }
	]

Add the following to your project's `composer.json` file:

	"require-dev": {
	    "behat/mink-selenium2-driver": "*",
    	"magento-ecg/coding-standard": "dev-master",
		"magetest/magento-behat-extension": "dev-feature/Behat3",
    	"phpunit/phpunit": "*",
	    "studioforty9/qa": "dev-master",
	    "alexandresalome/mailcatcher": "dev-master",
	    "emuse/behat-html-formatter": "0.1.*"
	  },
	  "config": {
    	"bin-dir": "bin"
	  },
	  "autoload": {
    	"psr-0": {
	      "": [
    	    "./app",
        	"./app/code/local",
	        "./app/code/community",
    	    "./app/code/core",
        	"./lib"
	      ]
    	}
	  },
	  "scripts": {
    	"post-install-cmd": [
	      "chmod +x ./bin/sf9-qa-post-install.sh",
    	  "./bin/sf9-qa-post-install.sh"
    	]
	  }
	  
Once the composer installation has completed, you should be able to run `./bin/behat/` from your Magento project root and have your Behat tests run.

##Notes

###Debugging
- Screenshots of failed scenarios are saved to to `{magento_root}/var/behat/screenshots/`
- Some information is logged to `{magento_root}/var/log/behat.log`
- An email with the results can be sent out provided that
	- The email recipient is set in the system configuration under System -> Configuration -> Advanced -> QA
	- In `behat.yml` uncomment the html formatter
	- **Currently not working**