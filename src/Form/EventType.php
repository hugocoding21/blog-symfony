<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add("name", null, ["label" => "Nom"])
            ->add("price", null, [
                "label" => "Prix",
                "required" => false,
                "empty_data" => "Gratuit",
            ])
            ->add("startAt", null, [
                "widget" => "single_text",
                "label" => "Début",
            ])
            ->add("endAt", null, ["widget" => "single_text", "label" => "Fin"])
            ->add("posterFile", FileType::class, [
                "label" => "Image",
                "required" => false,
            ])
            ->add("place", null, [
                "choice_label" => "city",
                "label" => "Ville",
            ])

            ->add("categories", null, [
                "choice_label" => "name",
                "expanded" => true,
                "label" => "Catégories",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Event::class,
        ]);
    }
}
