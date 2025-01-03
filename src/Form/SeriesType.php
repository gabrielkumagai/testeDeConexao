<?php
//define o nome como um form
namespace App\Form;

// define os componentes que serao usados
use App\DTO\SeriesCreateFromInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// a classe seriesTypo extende de um typoabstratio
class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void // uma função que faz a contrução do form, sendo passado como parametros o buider e as opções
    { 
        $builder // o builder vai adicionando os campos
            ->add('seriesName', options: ['label' => 'Nome:']) // series name tendo o label como nome
            ->add('seasonsQuantity', NumberType::class, options: ['label' => 'Qtd Temporadas:']) // a quantidade de temporadas sendo um tipo numerico com o label qtd temporadas
            ->add('episodesPerSeason', NumberType::class, options: ['label' => 'Ep por Temporada:']) // o episodeos por temporada sendo um tipo numerico 
            ->add('save', SubmitType::class, ['label' => $options['is_edit'] ? 'Editar' : 'Adicionar']) // um save sendo ele um typo de envio se a opção is_edit for true o label sera editar, se nao ele sera adicionar
            
            ->setMethod($options['is_edit'] ? 'PUT' : 'POST') // vai definir o modo tomando como base se a opção é is_edit é true ou false
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void // a função que configura a opção
    {
        $resolver->setDefaults([
            'data_class' => SeriesCreateFromInput::class,
            'is_edit' => false,
        ]);

        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}

/*
<?php

// Define o namespace para a classe, indicando que ela faz parte da pasta "Form"
namespace App\Form;

// Importa as classes necessárias do Symfony para trabalhar com formulários
use App\DTO\SeriesCreateFromInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// A classe SeriesType estende a classe AbstractType do Symfony, que é a base para criar formulários
class SeriesType extends AbstractType
{
    // Método que constrói o formulário, onde definimos os campos do formulário
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder // O builder é usado para adicionar campos ao formulário
            ->add('seriesName', ['label' => 'Nome:']) // Adiciona um campo de texto para o nome da série, com o label "Nome:"
            ->add('seasonsQuantity', NumberType::class, ['label' => 'Qtd Temporadas:']) // Adiciona um campo numérico para a quantidade de temporadas, com o label "Qtd Temporadas:"
            ->add('episodesPerSeason', NumberType::class, ['label' => 'Ep por Temporada:']) // Adiciona um campo numérico para a quantidade de episódios por temporada, com o label "Ep por Temporada:"
            ->add('save', SubmitType::class, ['label' => $options['is_edit'] ? 'Editar' : 'Adicionar']) // Adiciona um botão de envio, com o texto dinâmico "Editar" ou "Adicionar" dependendo da opção 'is_edit'
            
            // Define o método HTTP (POST ou PATCH) dependendo se o formulário é para editar ou criar
            ->setMethod($options['is_edit'] ? 'PATCH' : 'POST') 
        ;
    }

    // Método que configura as opções do formulário
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesCreateFromInput::class, // Define que os dados do formulário serão mapeados para a classe SeriesCreateFromInput
            'is_edit' => false, // Define o valor padrão da opção 'is_edit' como false (formulário de criação)
        ]);

        // Define que a opção 'is_edit' deve ser do tipo booleano (true ou false)
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
    */

