<?php

namespace DynamicJobTabBundle\Controller;

use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Configuration Controller
 */
class ConfigurationController 
{
    /** @var IdentifiableObjectRepositoryInterface */
    private $jobInstanceRepository;

    
    public function __construct(IdentifiableObjectRepositoryInterface $jobInstanceRepository)
    {
        $this->jobInstanceRepository = $jobInstanceRepository; 
    }

    /**
     * to fetch the job code by job instance code
     * 
     * @param $identifier
     * 
     * @return JsonResponse
     */
    public function getJobCodeByInstanceCode($identifier) 
    {
        $jobInstance = $this->jobInstanceRepository->findOneByIdentifier($identifier);

        return new JsonResponse($jobInstance?->getJobName());
    }
}
