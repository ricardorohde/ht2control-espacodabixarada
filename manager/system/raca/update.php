<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;

$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
?>

<div class="row row-centered">
    <div class="col-lg-12" id="crop-avatar">

        <!-- Categoria form -->

        <div class="bs-callout bs-callout-default">
            <h4>Criar Categoria</h4>
        </div>

        <?php

        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $Id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($data && $data['Atualiza']):
            unset($data['Atualiza']);

            require('_models/AdminCategoria.class.php');
            $cadastra = new AdminCategoria();
            $cadastra->ExeUpdate($Id, $data);

            if ($cadastra->getResult()):
                Message::FlashMsg("msgAlert", WS_ACCEPT ,"Categoria ".$data['categoria_nome']." foi atualizado com sucesso!", true);
                header('Location: painel.php?exe=raca/index&bold='.$Id);
            else:
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            endif;
        else:
            $ReadUser = new Read;
            $ReadUser->ExeRead("app_categorias", "WHERE categoria_id = :id", "id={$Id}");
            if (!$ReadUser->getResult()):
                Message::FlashMsg("msgAlert", WS_ERROR ,"Usuário não encontrado", true);
                header('Location: painel.php?exe=raca/index');
            else:
                $data = $ReadUser->getResult()[0];
                unset($data['user_password']);
            endif;
        endif;
        ?>

        <!-- Current avatar -->
        <div class="avatar-view" title="">
            <img src="<?php if(!empty($data['categoria_img'])){echo HOME."/".$data['categoria_img'];}else{echo "../uploads/avatar/avatar.jpg";}?>" alt="Avatar">
        </div>

        <!-- Cropping modal -->
        <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog"
             tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form class="avatar-form" action="system/helpers/crop.php" enctype="multipart/form-data"
                          method="post">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="avatar-modal-label">Seleciona a imagem</h4>
                        </div>
                        <div class="modal-body">
                            <div class="avatar-body">

                                <!-- Upload image and data -->
                                <div class="avatar-upload">
                                    <input type="hidden" class="avatar-src" name="avatar_src">
                                    <input type="hidden" class="avatar-data" name="avatar_data">
                                    <label for="avatarInput">Buscar:</label>
                                    <input type="file" class="avatar-input" id="avatarInput" name="avatar_file">
                                </div>

                                <!-- Crop and preview -->
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="avatar-wrapper"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="avatar-preview preview-lg"></div>
                                        <div class="avatar-preview preview-md"></div>
                                        <div class="avatar-preview preview-sm"></div>
                                    </div>
                                </div>

                                <div class="row avatar-btns">
                                    <div class="col-md-9">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="-90" title="Rotate -90 degrees">Girar Esq.
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="-15">-15deg
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="-30">-30deg
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="-45">-45deg
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="45">45deg
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="30">30deg
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="15">15deg
                                            </button>
                                            <button type="button" class="btn btn-primary" data-method="rotate"
                                                    data-option="90" title="Rotate 90 degrees">Girar Dir.
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-block avatar-save">Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div><!-- /.modal -->

        <form class="form-horizontal" method="post" name="form_tipoevento" enctype="multipart/form-data">
            <fieldset>
                <div class="col-md-12">
                    <label for="basic-url">Nome</label>
                    <input type="text" class="form-control" name="categoria_nome" maxlength="50"
                           value="<?php if (isset($data['categoria_nome'])) {
                               echo $data['categoria_nome'];
                           } ?>">
                </div>
                <div class="col-md-12">
                    <label for="basic-url">Tipo Evento</label>
                    <select class="form-control" name="tipoevento_cod">
                        <option value="" disabled selected>Selecione...</option>
                        <?php
                        $tipoevento = new Read;
                        $tipoevento->ExeRead("app_tipoevento", "WHERE tipoevento_status = 1 ORDER BY tipoevento_nome");
                        if ($tipoevento->getResult()):
                            foreach ($tipoevento->getResult() as $tipoevento):
                                extract($tipoevento);
                                ?>
                                <option
                                    value="<?= $tipoevento_id ?>" <?php if (isset($data['tipoevento_cod']) and ($data['tipoevento_cod'] == $tipoevento_id)) {
                                    echo "selected";
                                } ?>>
                                    <?= $tipoevento_nome ?>
                                </option>
                                <?php
                            endforeach;
                        else:
                        endif;
                        ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="basic-url">Descrição</label>
                    <textarea type="text" class="form-control"
                              name="categoria_descricao"><?php if (isset($data['categoria_descricao'])) {
                            echo $data['categoria_descricao'];
                        } ?></textarea>
                </div>
                <div class="col-md-12">
                    <label for="basic-url">Status</label>
                    <select class="form-control" name="categoria_status">
                        <option
                            value="0" <?php if (isset($data['categoria_status']) and ($data['categoria_status'] == "0")) {
                            echo "selected";
                        } ?>>Inativo
                        </option>
                        <option
                            value="1" <?php if (isset($data['categoria_status']) and ($data['categoria_status'] == "1")) {
                            echo "selected";
                        } ?>>Ativo
                        </option>
                    </select>
                </div>
                <input type="hidden" name="categoria_img" id="img_crop" value="<?php if(!empty($data['categoria_img'])){echo $data['categoria_img'];}?>">

                <div class="clearfix"></div>

                <hr>

                <div class="col-md-6">
                    <button class="btn btn-danger btn-block">Voltar</button>
                </div>
                <div class="col-md-6">
                    <input type="submit" class="btn btn-primary btn-block" value="Enviar" name="Atualiza">
                </div>

            </fieldset>
        </form>
    </div>
</div>

<!-- Cropper JS -->
<link rel="stylesheet" href="../res/cropper/cropper.min.css">
<link rel="stylesheet" href="../res/cropper/main.css">
<script src="../res/cropper/cropper.min.js"></script>
<script src="../res/cropper/main.js"></script>




