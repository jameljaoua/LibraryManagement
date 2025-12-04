<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(
        int $id,
        LivreRepository $livreRepository,
        CartRepository $cartRepository,
        EntityManagerInterface $em
    ): Response {
        $livre = $livreRepository->find($id);
        
        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($this->getUser());
            $em->persist($cart);
        }

        // Vérifier si le livre existe déjà dans le panier
        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getLivre()->getId() === $livre->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setLivre($livre);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
        }

        $em->flush();

        $this->addFlash('success', 'Livre ajouté au panier !');
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(
        int $id,
        CartRepository $cartRepository,
        EntityManagerInterface $em
    ): Response {
        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        
        if ($cart) {
            foreach ($cart->getItems() as $item) {
                if ($item->getId() === $id) {
                    $em->remove($item);
                    break;
                }
            }
            $em->flush();
        }

        $this->addFlash('success', 'Article retiré du panier');
        
        return $this->redirectToRoute('app_cart');
    }
}