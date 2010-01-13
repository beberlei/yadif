<?php

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
     ->setName('Yadif')
     ->setChannel('beberlei.pearfarm.org')
     ->setSummary('Simple Dependency Injection Container')
     ->setDescription('Simple Dependency Injection Container')
     ->setReleaseVersion('0.9.1')
     ->setReleaseStability('beta')
     ->setApiVersion('0.9.1')
     ->setApiStability('beta')
     ->setLicense(Pearfarm_PackageSpec::LICENSE_BSD)
     ->setNotes('Yadif - Yet Another Dependency Injection Framework

    * Originally by Thomas McKelvey (https://github.com/tsmckelvey/yadif/tree)
    * Fork by Benjamin Eberlei (https://github.com/beberlei/yadif/tree)

Inject dependencies via a very simple configuration mechanism.')
     ->addMaintainer('lead', 'Benjamin Eberlei', 'beberlei', 'kontakt@beberlei.de')
     ->addMaintainer('lead', 'Tom McKelvey', 'tsmckelvey', '')
     ->addGitFiles()
     ->addFilesRegex('/Tests/', 'test')
     ->addFilesSimple(array('LICENSE.txt', 'README.markdown'), 'doc')
     ->addExcludeFiles(array('.gitignore', 'package.xml', 'pearfarm.spec'))
     ->setDependsOnPHPVersionMin('5.2.0');