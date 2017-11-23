<?php

namespace AppBundle\Controller\Front;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;

class AppController extends Controller
{
    const CONFIG_FILE_PATH = __DIR__.('/../../../../app/config/page/');
    const METRICS_FILE_PATH = __DIR__.('/../../../../app/config/metrics/');
    const DATE_FORMAT = 'Y-m-d';



    public function getMetricsCodeAction($metricsName)
    {
        $metricsFile = self::METRICS_FILE_PATH . $metricsName . '.yml';

        $metricsContent = Yaml::parse(file_get_contents($metricsFile));

        return Http\Response::create($metricsContent);
    }


    /**
     * @param string $entity
     * @return mixed
     */
    protected function getCategoryData(string $entity) {

        return $this->getEntityRepository('category')->findOneByEntity($entity);

    }

    /**
     * @param string $class
     * @param array $orderBy
     * @param \DateTime $filterDate
     * @param string $selectField
     * @param int|null $limit
     * @return array
     */
    protected function getSortedList(string $class, array $orderBy, \DateTime $filterDate = null, string $selectField = null, int $limit = null)
    {

        /** @var EntityManager $repository */
        $repository = $this->getDoctrine()->getRepository($class);

        $qb = $repository->createQueryBuilder('a');


        if ($selectField && $filterDate) {
            $qb->where('a.' . $selectField . ' > :filterdate')
                ->setParameter('filterdate', $filterDate);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }


        $qb = $qb
            ->orderBy('a.' . key($orderBy), current($orderBy))
            ->getQuery();

        return $qb->getResult();
    }

    /**
     * @param string $response
     * @return mixed|string
     */
    protected function googleRecaptchaVerifyer(string $response)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getParameter('recaptcha_verify_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
                "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"secret\"\r\n\r\n" . $this->getParameter('recaptcha_secret_key') . "\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"response\"\r\n\r\n" . $response . "\r\n-----011000010111000001101001--\r\n",
            CURLOPT_HTTPHEADER => array(
                "content-type: multipart/form-data; boundary=---011000010111000001101001"
            ),
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    /**
     * @param string $entity
     * @return string
     */
    protected function getClassName(string $entity) {

        $className = ucfirst($entity);

        $class = 'AppBundle\\Entity\\'.$className;

        return $class;
    }

    /**
     * @param string $entity
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getEntityRepository(string $entity) {

        return $this->getDoctrine()->getRepository($this->getClassName($entity));

    }
}