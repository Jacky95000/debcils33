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
use DateTime;

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
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false, // Pour Flatpickr
                'attr' => ['class' => 'form-control datepicker'],
                'label' => 'Date du rendez-vous',
            ])
            ->add('heure', HiddenType::class);

        // Transformer heure string <-> DateTime
        $builder->get('heure')->addModelTransformer(new HeureToDateTimeTransformer());

        // Event pour définir durée selon prestation
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $rdv = $event->getData();

            if (is_string($rdv->getDate())) {
                $date = DateTime::createFromFormat('Y-m-d', $rdv->getDate());
                if ($date !== false) {
                    $rdv->setDate($date);
                }
            }

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
