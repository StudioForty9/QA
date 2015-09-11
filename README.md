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
	    }
	]

Add the following to your project's `composer.json` file:

	"require-dev": {
	    "behat/mink-selenium2-driver": "*",
    	"magento-ecg/coding-standard": "dev-master",
	    "magetest/magento-behat-extension": "dev-develop",
    	"phpunit/phpunit": "*",
	    "studioforty9/qa": "dev-master"
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

###Custom Features
To add tests for custom features, do the following:

- Add the name of your custom feature to the `behat.yml` file:

		custom_contexts:
    		- customfeature1
	        - customfeature2
	        
- Add a feature file under features/custom/ (e.g., `features/custom/customfeature1.feature`)
- Add a custom context class under features/bootstrap/custom/ (e.g., `features/bootstrap/custom/Customfeature1.php`)

###Debugging
- Screenshots of failed scenarios are saved to to `{magento_root}/var/behat/screenshots/`
- Some information is logged to `{magento_root}/var/log/behat.log`
- An email with the results can be sent out provided that
	- The email recipient is set in the system configuration under System -> Configuration -> Advanced -> QA
	- Behat is run using the following command: 
	
			./bin/behat --format=html --out=var/behat/results.html