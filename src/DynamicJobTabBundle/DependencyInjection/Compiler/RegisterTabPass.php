<?php

namespace DynamicJobTabBundle\DependencyInjection\Compiler;

use Akeneo\Platform\Bundle\ImportExportBundle\Provider\Form\JobInstanceFormProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * RegisterTabPass
 */
class RegisterTabPass implements CompilerPassInterface
{
    const PROVIDER_ID = 'pim_enrich.provider.form';
    const FORM_EXTENSION_ID = 'pim-job-instance-csv-product-export-edit-properties2';
    const JOBS_FOR_EXTRA_TAB_ID = 'pim_job_instance_extra_tab_jobs';
    const CHAINED_PROVIDER_ID = 'pim_enrich.provider.form.chained';
    const DIR_PATH = __DIR__ . '/../../Resources/config/form_extensions';

    protected $defaultExtensionValue;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->removeOldMapping();
        if ($container->hasParameter(static::JOBS_FOR_EXTRA_TAB_ID)) {
            $jobsCodes = $container->getParameter(static::JOBS_FOR_EXTRA_TAB_ID);
    
            if (!is_array($jobsCodes)) {
                return;
            }
    
            $allJobForms = $this->getAllJobForms($container);
 
            foreach ($jobsCodes as $jobCode) {
                if (in_array($jobCode, array_keys($allJobForms))) {
                    $jobForm = $allJobForms[$jobCode];
                    $extensionValue = $this->getExtensionValue($jobForm);
                    $preparedExtensionValue = $this->getPreparedExtensionConfiguration($jobsCodes, $jobForm, $extensionValue);
                    $this->addExtensionValue($jobForm, $preparedExtensionValue);
                }
            } 
        }
    }

    /**
     * remove the old mapping of autogenerated directory form tabs
     */
    protected function removeOldMapping()
    {
        array_map('unlink', array_filter((array) glob(realpath(static::DIR_PATH) . "/formTabs/*")));
    }

    /**
     * get the existing extension value as per the jobform if not found then return default template.
     */
    protected function getExtensionValue($jobForm): array
    {
        $formExtensionValue = $this->getDefaultFormExtensionValue();

        $filePath = realpath(static::DIR_PATH) . "/formTabs/$jobForm.yml";
        if (file_exists($filePath) ) {
            $formExtensionValue = Yaml::parseFile(realpath(static::DIR_PATH) . "/formTabs/$jobForm.yml"); 
        }
        
        return $formExtensionValue;
    }

    /**
     * it add dump the extensions in the autogenerated director form tabs.
     * 
     */
    protected function addExtensionValue($jobForm, $preparedExtensionValue)
    {
        $yaml = Yaml::dump($preparedExtensionValue, 5);
        $filePath = realpath(static::DIR_PATH) . "/formTabs/$jobForm.yml";
        file_put_contents($filePath, $yaml);
    }

    /**
     * it return the defualt template for the form.
     * 
     */
    protected function getDefaultFormExtensionValue()
    {
        if (!$this->defaultExtensionValue) {
            $this->defaultExtensionValue = Yaml::parseFile(realpath(static::DIR_PATH) . '/tab_template.yml');
        }

        return $this->defaultExtensionValue;
    }

    /**
     * create a new form extension based on the default template. 
     * 
     */
    protected function getPreparedExtensionConfiguration(array $jobsCodes, string $form, array $defaultExtensionValue): array
    {
        $extensionValue = $defaultExtensionValue['extensions'] ?? [];
        $newExtensionValue = [];
        foreach($extensionValue as $key => $value) {
            $newKey = str_replace('_form_name_', $form, $key);
            $value['parent'] = str_replace('_form_name_', $form, $value['parent']);
            if ("$form-edit-custom_tab" === $newKey) {
                $value['config']['whitelistJobs'] = $jobsCodes;
            }
            $newExtensionValue[$newKey] = $value; 
        }

        return ['extensions' => $newExtensionValue];
    }


    /**
     * fetch all the job forms.
     *  
     * @params ContainerBuilder $container
     * @return array
     */
    protected function getAllJobForms(ContainerBuilder $container): array
    {
        $jobForm = [];
        $providerDefinitionIds = $container->findTaggedServiceIds(static::PROVIDER_ID);
        
        foreach (array_keys($providerDefinitionIds) as $id) {
            if (!$container->hasDefinition($id)) {
                continue;
            }
            $definition = $container->getDefinition($id);
            if ($definition->getClass() === JobInstanceFormProvider::class) {
                $jobForm = array_merge($jobForm, $definition->getArguments()[0] ?? []);
            }
        }

        return $jobForm;
    }
}