# DynamicJobTabBundle

Dynamic Job Tab Bundle: Seamlessly add custom tabs to any job with the Dynamic Job Tab Bundle. This versatile tool allows you to dynamically integrate a custom tab into your job listings. To configure the job name, simply update the configuration.yml file.


# Installation

Manual Installation:

- merge "src" directory with the akeneo project src directory.

- register bundle in config/bundles.php

    \DynamicJobTabBundle\DynamicJobTabBundle::class => ['all' => true],

- copy the routing file from attached file to the akeneo config/routes directory. after the verify the entry in the file config/routes/dynamicTab.yml

- then run the installation command

    NO_DOCKER=true make upgrade-front


# uses

- add the job names in the configuration.yml
- run the command 
    NO_DOCKER=true make upgrade-front

- you can see the new custom tab in the given job names.


Note: you can change the custom tab fields by changing the default template config/form_extensions/tab_template.yml
