<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(
        CartRepository $cartRepository,
        EntityManagerInterface $em
    ): Response {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setTotalPrice($cart->getTotal());
        $order->setStatus('pending');

        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrderRelation($order);
            $orderItem->setLivre($cartItem->getLivre());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getLivre()->getPrix());
            $em->persist($orderItem);
        }

        $em->persist($order);
        
        // Vider le panier
        foreach ($cart->getItems() as $item) {
            $em->remove($item);
        }
        $em->remove($cart);
        
        $em->flush();

        return $this->redirectToRoute('app_order_payment', ['id' => $order->getId()]);
    }

    #[Route('/payment/{id}', name: 'app_order_payment')]
    public function payment(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/payment.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/confirm/{id}', name: 'app_order_confirm')]
    public function confirm(Order $order, EntityManagerInterface $em): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $order->setStatus('paid');
        $em->flush();

        $this->addFlash('success', 'Commande confirmée avec succès !');
        
        return $this->redirectToRoute('app_order_success', ['id' => $order->getId()]);
    }

    #[Route('/success/{id}', name: 'app_order_success')]
    public function success(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/history', name: 'app_order_history')]
    public function history(): Response
    {
        return $this->render('order/history.html.twig');
    }
}