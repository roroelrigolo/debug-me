<?php

namespace App\Form;

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
            ->add('title')
            ->add('content', CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    'toolbar' => [
                        ['Bold','Italic','Underline','-','NumberedList', 'BulletedList','-','Link','-','Image','Table']
                    ],
                    'language' => 'fr',
                ),
            ))
            ->add('author', EntityType::class, array(
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
