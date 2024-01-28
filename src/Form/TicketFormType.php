<?php

namespace App\Form;

use App\Entity\StatutTicket;
use App\Entity\Tag;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',null, array(
                'label' => "Titre",
                'required' => true
            ))
            /*
            ->add('content', CKEditorType::class, array(
                'label' => "Contenu",
                'config' => array(
                    'uiColor' => '#ffffff',
                    'toolbar' => [
                        ['Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','-','Image','Table']
                    ],
                    'language' => 'fr',
                ),
            ))*/
            ->add('content', null, array(
                'label' => "Contenu",
                'attr' => ['rows' => 10],
            ))
            ->add('statut', EntityType::class, array(
                'label' => "Statut",
                'class'     => StatutTicket::class,
                'expanded'  => false,
                'multiple'  => false,
                'choice_label' => 'name',
                'choice_value' => 'id',
            ))
            ->add('author', EntityType::class, array(
                'label' => "Auteur",
                'class'     => User::class,
                'expanded'  => false,
                'multiple'  => false,
                'choice_label' => 'username',
                'choice_value' => 'id',
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');
                }
            ))
            ->add('tags', EntityType::class, array(
                'class'     => Tag::class,
                'expanded'  => true,
                'multiple'  => true,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'query_builder' => function (TagRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
