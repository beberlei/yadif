<?php

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('Yadif')
             ->setChannel('beberlei.pearfarm.org')
             ->setSummary('Simple Dependency Injection Container')
             ->setDescription('Simple Dependency Injection Container')
             ->setReleaseVersion('0.9.0')
             ->setReleaseStability('beta')
             ->setApiVersion('0.9.0')
             ->setApiStability('beta')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_MIT)
             ->setNotes('Initial releas eon Pearfarm')
             ->addMaintainer('lead', 'Benjamin Eberlei', 'beberlei', 'kontakt@beberlei.de')
             ->addGitFiles()
             ->addFilesRegex('/Tests/', 'test')
             ->addFilesSimple(array('LICENSE.txt', 'README.markdown'));