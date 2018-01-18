<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\File;
use AppBundle\Entity\History;
use AppBundle\Entity\News;
use AppBundle\Entity\Review;
use AppBundle\Entity\Category;
use AppBundle\Entity\Banner;
use AppBundle\Entity\User;
use AppBundle\Form\AbstractFormType;
use AppBundle\Form\NewsType;
use AppBundle\Form\UserType;
use AppBundle\Service\FileUploaderService;
use AppBundle\Service\MailerService;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;


class AdminController extends Controller
{
    const CONFIG_FILE_PATH = __DIR__ . ('/../../../../app/config/page/');
    const METRICS_FILE_PATH = __DIR__ . ('/../../../../app/config/metrics/');
    const ROBOTS = __DIR__ . ('/../../../../web/robots.txt');
    const HTACCESS = __DIR__ . ('/../../../../web/.htaccess');
    const ENTITY_NAMESPACE = 'AppBundle\\Entity\\';

    protected $configFilePath;
    public $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @Route("/admin/dashboard", name="admin.index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render(':default/admin:index.html.twig', [
            'categoryData' => $em->getRepository(Category::class)->findOneByEntity('index')
        ]);
    }

    /**
     * @Route("/admin/users", name="admin.user.list")
     */
    public function usersList() {
        $users = $this->getEntityRepository('user')->findAll();

        return $this->render(':default/admin/list:users.html.twig', [
            'users' => $users,
            'userRoles' => array_flip(User::USER_ROLES)
        ]);
    }

    /**
     * @Route("/admin/users/add", name="admin.user.add")
     * @Method({"POST", "GET"})
     */
    public function addUserAction(Request $request, UserPasswordEncoderInterface $encoder) {

        $user = new User();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    /** @var User $formData */
                    $formData = $form->getData();

                    $userManager = $this->get('fos_user.user_manager');

                    $encoded = $encoder->encodePassword($formData, $formData->getPassword());

                    $formData->setPassword($encoded);

                    $formData->setRoles([$request->request->get('user')['roles']]);

                    $userManager->updateUser($formData);
                }
            }
        }

        return $this->render('default/admin/manage/user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response;
     * @Route("/admin/users/manage/{user}", name="admin.user.manage")
     * @Method({"POST", "GET"})
     */
    public function manageUserAction(User $user, Request $request, UserPasswordEncoderInterface $encoder) {

        $form = $this
            ->createForm(UserType::class, $user)
            ->remove('password')
            ->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    /** @var User $formData */
                    $formData = $form->getData();

                    $userManager = $this->get('fos_user.user_manager');

                    $encoded = $encoder->encodePassword($formData, $formData->getPassword());

                    $formData->setPassword($encoded);

                    $formData->setRoles([$request->request->get('user')['roles']]);

                    $userManager->updateUser($formData);
                }
            }
        }

        return $this->render('default/admin/manage/user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @param string $className
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUnreadNotifications(string $className, array $criteria, array $orderBy, int $limit = null, int $offset = null)
    {

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository($className);

        $object = $repository->findBy($criteria, $orderBy, $limit, $offset);

        return $object;
    }

    /**
     * @param string $entity
     *
     * @return Response
     * @Route("/admin/{entity}/list", name="admin.list")
     */
    public function listAction(string $entity)
    {

        $em = $this->getDoctrine()->getManager();

        $class = ucfirst($entity);

        $repository = $em->getRepository('AppBundle\\Entity\\' . $class);

        $categoryData = $em->getRepository(Category::class)->findOneByEntity($entity);

        return $this->render(':default/admin:list.html.twig', [
            'objects' => $repository->findAll(),
            'pageSeo' => $this->getStaticPageSeo($entity),
            'pageName' => $entity,
            'categoryData' => $categoryData
        ]);
    }

    /**
     * @param string $entity
     * @param int|null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @Route("/admin/{entity}/manage/{id}", name="admin.manage")
     */
    public function manageAction(string $entity, int $id = null, Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        $view = ':default/admin/manage:'.$entity.'.html.twig';

        $uploader = $this->get(FileUploaderService::class);

        $files = null;

        $className = ucfirst($entity);

        $class = $this->getClassName($entity);

        $object = new $class();

        if ($id) {
            $object = $em->getRepository($class)->findOneById($id);
        }

        $form = $this->entityFormBuilder($className, $object);

        if (new $object instanceof Review) {
            $form->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form
                ->getData();

            if (!new $object instanceof Review) {
                $formData->setAuthor($this->getUser());
            }

            if ($formData instanceof History) {
                $history = $em->getRepository(History::class)->findOneBy(['isEnabled' => 1]);
                if ($history) {
                    $history->setEnabled(0);
                }
            }

            if (new $object instanceof Review) {
                $formData->setStatus(1);
                $formData->setApproved(1);
            }

            if ($formData instanceof Banner) {
                if ($formData->isLink()) {
                    if (isset($request->request->get('banner')['object'])) {
                        $formData->setObject($request->request->get('banner')['object']);
                    }
                }
            }


            $em->persist($formData);
            $em->flush();

            if (isset($form['files'])) {
                $attachedFiles = $form['files']->getData();

                if ($attachedFiles instanceof UploadedFile) {
                    $em->persist(
                        $this->photoUploader($uploader, $entity, $attachedFiles, $formData, $class)
                    );
                }

                if (!empty($attachedFiles)) {


                    if ($object instanceof Banner) {
                        if (!empty($form['files'])) {
                            $this->deleteObjectRelatedFiles($class, $object->getId());
                        }

                    }

                    foreach ($attachedFiles as $attachedFile) {

                        $file = new File();

                        $uploader
                            ->setDir($entity)
                            ->setFile($attachedFile);

                        $file
                            ->setForeignKey($formData->getId())
                            ->setMimeType(strtolower($uploader->getMimeType()))
                            ->setEntity($class)
                            ->setName($uploader->upload());

                        $em->persist($file);

                    }

                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin.manage', [
                'entity' => $entity,
                'id' => $formData->getId()
            ]);
        }

        return $this->render($view, [
            'form' => $form->createView(),
            'object' => $object,
        ]);
    }

    /**
     * @param FileUploaderService $uploader
     * @param string $entity
     * @param UploadedFile $uploadedFile
     * @param $formData
     * @param string $class
     * @return File
     */
    private function photoUploader(FileUploaderService $uploader, string $entity, UploadedFile $uploadedFile, $formData, string $class)
    {
        $file = new File();

        $uploader
            ->setDir($entity)
            ->setFile($uploadedFile);

        $file
            ->setForeignKey($formData->getId())
            ->setMimeType($uploader->getMimeType())
            ->setEntity($class)
            ->setName($uploader->upload());

        return $file;
    }

    /**
     * @param $entity
     * @param $id
     * @return Response
     * @Route("/admin/{entity}/manage/{id}/files", name="admin.manage.files")
     */
    public function fileManagerAction(string $entity, int $id)
    {


        return $this->render(':default/admin:files.html.twig', [
            'files' => $this->fileLoader($this->getClassName($entity), $id),
            'imagesExt' => FileUploaderService::IMAGES,
            'videosExt' => FileUploaderService::VIDEOS,
        ]);
    }

    /**
     * @param $className
     * @param $object
     * @return Form
     */
    private function entityFormBuilder($className, $object)
    {

        $formName = 'AppBundle\Form\\' . $className . 'Type';

        $form = $this->createForm($formName, $object);

        return $form;

    }

    /**
     * @param string $class
     * @param int $id
     * @return array
     */
    private function fileLoader(string $class, int $id)
    {

        $doctrine = $this->getDoctrine();

        $files = $doctrine->getRepository(File::class)->findBy(
            ['foreignKey' => $id, 'entity' => $class]
        );

        return $files;

    }

    /**
     * @param string $entity
     * @return string
     */
    protected function getClassName(string $entity)
    {

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\' . $className;

        return $class;
    }

    /**
     * @param string $entityClassName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getEntityRepository(string $entityClassName)
    {

        return $this->getDoctrine()->getRepository($this->getClassName($entityClassName));

    }

    /**
     * @param string $objectClass
     * @param int $objectId
     * @return bool
     */
    protected function deleteObjectRelatedFiles(string $objectClass, int $objectId)
    {

        $em = $this->getDoctrine()->getManager();

        $objectFiles = $em->getRepository(File::class)->findBy([
            'entity' => $objectClass,
            'foreignKey' => $objectId,
        ]);

        foreach ($objectFiles as $objectFile) {
            $this->deleteFile($objectFile);
        }

        return true;
    }

    /**
     * @param File $file
     * @return Response
     */
    protected function deleteFile(File $file)
    {

        $em = $this->getDoctrine()->getManager();

        $finder = new Finder();

        $fileDir = $this->getParameter('upload_directory');

        $finder->name($file->getName());

        foreach ($finder->in($fileDir) as $item) {
            unlink($item);
        }

        $em->remove($file);

        $em->flush();

        return JsonResponse::create('ok');
    }

    protected function getStaticPageSeo($pageName = 'index')
    {
        $fileName = $pageName . '.yml';

        $config = $this->yamlParse($fileName, self::CONFIG_FILE_PATH);

        return $config;

    }

    protected function getMetricsCode($metricsType)
    {

        $fileName = $metricsType . '.yml';

        $metrics = $this->yamlParse($fileName, self::METRICS_FILE_PATH);

        return $metrics;

    }

    protected function yamlParse($fileName, $filePath)
    {

        $configPath = $filePath . $fileName;

        if (!file_exists($configPath)) {
            copy($filePath . 'default.yml', $configPath);
        }

        $yaml = Yaml::parse(file_get_contents($configPath));

        return $yaml;

    }

    protected function yamlDump($pageName, $pageData, $filePath)
    {

        $pathToFile = $filePath . $pageName;

        $yaml = Yaml::dump($pageData);

        $dump = file_put_contents($pathToFile, $yaml);

        return $dump;
    }

    public function getFileContentAction(string $fileName)
    {

        $file = self::getConstants()[strtoupper($fileName)];

        if (file_exists($file)) {
            $handle = fopen($file, 'r');

            $fileContent = fread($handle, filesize($file));

            fclose($handle);

            return Response::create($fileContent);
        } else {
            return 'File ' . $file . ' not found!';
        }

    }

    protected static function getConstants()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

}
