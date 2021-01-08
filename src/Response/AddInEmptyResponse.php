<?php 

namespace App\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddInEmptyResponse
{    
    /**
     * className
     *
     * @var string
     */
    private $className;
    
    /**
     * uri
     *
     * @var string
     */
    private $uri;
    
    /**
     * manager
     *
     * @var EntityManagerInterface
     */
    private $manager;
    
    /**
     * request
     *
     * @var RequestStack
     */
    private $request;
    
    /**
     * normalizer
     *
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(RequestStack $request, EntityManagerInterface $manager, NormalizerInterface $normalizer)
    {
        $explode = explode('_', $request->getCurrentRequest()->get('_route'));
        $this->className = substr(ucfirst(end($explode)), 0, -1);

        $this->uri = $request->getCurrentRequest()->getUri();

        $this->manager = $manager;

        $this->request = $request;

        $this->normalizer = $normalizer;
        
    }
    
    /**
     * Method addInResponse
     * This method return the content of the response with added informations
     *
     * @param $existingContent 
     *
     * @return array
     */
    public function addInResponse($existingContent) 
    {
        if (class_exists('App\Entity\\' . $this->className)) {
            $repo = $this->manager->getRepository('App\Entity\\' . $this->className);

            if ($this->request->getCurrentRequest()->getMethod() == 'POST') {

                $newContent= $this->createdElement($repo, $existingContent);
            }

            if (in_array($this->request->getCurrentRequest()->getMethod(), ['PUT', 'PATCH'])) {
                
                $newContent = $this->updatedElement($repo, $existingContent); 
            }

            if ($this->request->getCurrentRequest()->getMethod() == 'DELETE') {
                $newContent = $existingContent;
            }
        } 
        return $newContent;
    }
    
    /**
     * Method createdElement
     * This method retrieve the just created element for add it in the content of the response
     *
     * @param $repo 
     * @param $content 
     *
     * @return array
     */
    private function createdElement($repo, $content)
    {
        $object = $repo->findLastId();
                
        $objectNormalize = $this->normalizer->normalize($object, null, [
            'groups' => 'show_' . strtolower($this->className) 
        ]);

        $content['_created']['data'] = $objectNormalize;

        $uri = 'GET ' . explode('?', $this->uri)[0]. '/' . $object->getId();
        $content['_links']['self'] = $uri;

        return $content;
    }
    
    /**
     * Method updatedElement
     * This method retrieve the just update element for add it in the content of the response
     * 
     * @param $repo 
     * @param $content 
     *
     * @return array
     */
    private function updatedElement($repo, $content)
    {
        $id = explode('/', $this->uri);
        $id = end($id);

        $object = $repo->find($id);

        $objectNormalize = $this->normalizer->normalize($object, null, [
            'groups' => 'show_' . strtolower($this->className) 
        ]);

        $content['_modify']['data'] = $objectNormalize;

        $uri = 'GET ' . explode('?', $this->uri)[0];
        $content['_links']['self'] = $uri;

        return $content;
    }
}