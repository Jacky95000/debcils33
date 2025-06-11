<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Form\DataTransformer\HeureToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prestation', ChoiceType::class, [
                'label' => 'Type de prestation',
                'choices' => [
                    'Réhaussement Cil 25€' => 'rehaussement',
                    'Réhaussement + Teinture 30€' => 'rehaussement_teinture',
                    'Extension de cil à cil 35€' => 'extension',
                ],
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'form-control datepicker', 'readonly' => true],
                'label' => 'Date du rendez-vous',
                'required' => true,
            ])
            ->add('heure', HiddenType::class, [
                'required' => true,
            ]);

        // Transformer pour convertir "14:30" en DateTime
        $builder->get('heure')->addModelTransformer(new HeureToDateTimeTransformer());

        // Fusion date + heure et calcul durée
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
    $rdv = $event->getData();

    $date = $rdv->getDate();
    $heure = $rdv->getHeure();

    // Forcer la conversion si date ou heure sont des strings (parfois arrive via JS)
    if (is_string($date)) {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
    }

    if (is_string($heure)) {
        $heure = \DateTime::createFromFormat('H:i', $heure);
    }

    // Si les deux sont valides, on fusionne
    if ($date instanceof \DateTime && $heure instanceof \DateTime) {
        $date->setTime((int)$heure->format('H'), (int)$heure->format('i'));
        $rdv->setDate($date); // Date finale avec heure
    }

    // Durée automatique selon prestation
    $durations = [
        'rehaussement' => 60,
        'rehaussement_teinture' => 120,
        'extension' => 180,
    ];

    $prestation = $rdv->getPrestation();
    if (isset($durations[$prestation])) {
        $rdv->setDuree($durations[$prestation]);
    }
});

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
