<?php
/**
 *
 * part-db version 0.1
 * Copyright (C) 2005 Christoph Lechner
 * http://www.cl-projects.de/
 *
 * part-db version 0.2+
 * Copyright (C) 2009 K. Jacobs and others (see authors.php)
 * http://code.google.com/p/part-db/
 *
 * Part-DB Version 0.4+
 * Copyright (C) 2016 - 2019 Jan Böhmer
 * https://github.com/jbtronics
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 *
 */

namespace App\Form;


use App\Entity\Attachments\Attachment;
use App\Entity\Attachments\AttachmentType;
use App\Entity\Base\StructuralDBElement;
use App\Form\Type\StructuralEntityType;
use App\Services\AttachmentHelper;
use App\Validator\Constraints\UrlOrBuiltin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttachmentFormType extends AbstractType
{
    protected $attachment_helper;
    protected $trans;
    protected $urlGenerator;
    protected $allow_attachments_download;

    public function __construct(AttachmentHelper $attachmentHelper, TranslatorInterface $trans,
                                UrlGeneratorInterface $urlGenerator, bool $allow_attachments_downloads)
    {
        $this->attachment_helper = $attachmentHelper;
        $this->trans = $trans;
        $this->urlGenerator = $urlGenerator;
        $this->allow_attachments_download = $allow_attachments_downloads;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => $this->trans->trans('attachment.edit.name')
        ])
            ->add('attachment_type', StructuralEntityType::class, [
                'label' =>  $this->trans->trans('attachment.edit.attachment_type'),
                'class' => AttachmentType::class,
                'disable_not_selectable' => true,
            ]);

        $builder->add('showInTable', CheckboxType::class, ['required' => false,
            'label' =>  $this->trans->trans('attachment.edit.show_in_table'),
            'attr' => ['class' => 'form-control-sm'],
            'label_attr' => ['class' => 'checkbox-custom']]);

        $builder->add('url', TextType::class, [
            'label' =>  $this->trans->trans('attachment.edit.url'),
            'required' => false,
            'attr' => [
                'data-autocomplete' => $this->urlGenerator->generate('typeahead_builtInRessources', ['query' => 'QUERY']),
                //Disable browser autocomplete
                'autocomplete' => 'off'
            ],
            'help' => $this->trans->trans('attachment.edit.url.help'),
            'constraints' => [
                $options['allow_builtins'] ? new UrlOrBuiltin() : new Url()
            ]
        ]);

        $builder->add('downloadURL', CheckboxType::class, ['required' => false,
            'label' =>  $this->trans->trans('attachment.edit.download_url'),
            'mapped' => false,
            'disabled' => !$this->allow_attachments_download,
            'attr' => ['class' => 'form-control-sm'],
            'label_attr' => ['class' => 'checkbox-custom']]);

        $builder->add('file', FileType::class, [
            'label' =>  $this->trans->trans('attachment.edit.file'),
            'mapped' => false,
            'required' => false,
            'attr' => ['class' => 'file', 'data-show-preview' => 'false', 'data-show-upload' => 'false'],
            'constraints' => [
                new File([
                    'maxSize' => $options['max_file_size']
                ])
            ]

        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attachment::class,
            'max_file_size' => '16M',
            'allow_builtins' => true
        ]);

    }
}