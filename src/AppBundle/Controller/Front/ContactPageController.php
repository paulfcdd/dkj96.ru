<?php

namespace AppBundle\Controller\Front;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation as Http;
use AppBundle\Entity as Entity;
use AppBundle\Service as Service;
use AppBundle\Form as Form;

class ContactPageController extends AppController
{
    /**
     * @param Http\Request $request
     * @return Http\Response
     * @Route("/contact", name="front.contact")
     */
    public function contactPageAction(Http\Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $form = $this
            ->createForm(Form\FeedbackType::class)
            ->handleRequest($request);


        if ($form->isSubmitted()) {

            $response = $request->request->get('g-recaptcha-response');

            $recaptchaVerifyer = $this->googleRecaptchaVerifyer($response);

            $recaptchaVerifyer = json_decode($recaptchaVerifyer);

            if ($form->isValid() && $recaptchaVerifyer->success) {

                $formData = $form->getData();

                /** @var Service\MailerService $mailer */
                $mailer = $this->get(Service\MailerService::class);

                $mailer
                    ->setTo($this->getParameter($formData->getToWhom()))
                    ->setBody($formData->getMessage())
                    ->setFrom($formData->getEmail())
                    ->setSubject('Новое сообщение');

                if (strpos($formData->getToWhom(), 'client_')) {
                    $mailer->setSubject(Entity\Feedback::TO_WHOM[$formData->getToWhom()]);
                }

                $em->persist($formData);

                try {
                    $em->flush();
                    $mailer->sendMessage();
                    $this->addFlash('success', 'Ваше сообщение успешно выслано.');
                } catch (DBALException $exception) {
                    $this->addFlash('error', 'Не удалось выслать сообщение, попробуйте позже');
                }
            } else {
                $this->addFlash('error', 'Вы должны подтвердить, что вы не робот');
            }
        }

        return $this->render(':default/front/page:kontakty.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}