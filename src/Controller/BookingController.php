<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\ApplicationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ad/reservez/{slug}", name="book_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

                $booking->setBooker($this->getUser())
                        ->setAd($ad);
                        if(!$booking->isBookableDates()){
                            $this->addFlash(
                                'warning',"les dates que vous avez choisi ne  peuvent pas être reservées elles sont deja prise"
                            );
                        }
                        else{
                            $manager->persist($booking);
                             $manager->flush();
                             return $this->redirectToRoute('booking_show',['id' =>$booking->getId(),
                          'withAlert'=>true
            
                          ]);
                        }
            
                    }
        return $this->render('booking/booking.html.twig', [
            'ad'=>$ad,
            'form'=> $form->createView()
        ]);
    }
    /**
     * Permet de voir une reservate sa route est appele dans redirectToRoute en haut
     *@Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @return Response
     */
    public function booking_show(Booking $booking){
        return $this->render('booking/show.html.twig',[
            'booking'=> $booking
        ]);

    }
}
