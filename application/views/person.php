<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CRUD</title>
        <link href="<?= base_url('./assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
        <link href="<?= base_url('./assets/datatables/css/dataTables.bootstrap.css')?>" rel="stylesheet">
        <link href="<?= base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')?>" rel="stylesheet">
    </head> 
    <body>
        <div class="container">
            <h2>CRUD - CodeIgniter by AJAX</h2>
            <br />
            <button class="btn btn-success" onclick="add_person()">
                <i class="glyphicon glyphicon-plus"></i> Adicionar
            </button>
            <button class="btn btn-default" onclick="reload_table()">
                <i class="glyphicon glyphicon-refresh"></i> Recarregar
            </button>
            <br /><br />
            <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Sobrenome</th>
                        <th>Sexo</th>
                        <th>Endereço</th>
                        <th>Data Nasc.</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <script src="<?= base_url('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
        <script src="<?= base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?= base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?= base_url('assets/datatables/js/dataTables.bootstrap.js') ?>"></script>
        <script src="<?= base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
        <script type="text/javascript">
            var save_method;
            var table;
            $(document).ready(function() {
                table = $('#table').DataTable({ 
                    "processing": true, 
                    "serverSide": true,
                    "order": [],
                    "ajax": {
                        "url": "<?= site_url('persons/ajax_list') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [{
                        "targets": [ -1 ],
                        "orderable": false,
                    }],
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ resultado por pagina",
                        "zeroRecords": "Nenhum registro encontrado",
                        "info": "Mostrando _PAGE_ de _PAGES_",
                        "infoEmpty": "No records available",
                        "infoFiltered": "(Filtrando de _MAX_ registros)",
                        "search": "Pesquisar",
                        "paginate": {
                            "next": "Próximo",
                            "previous": "Anterior"
                        }
                    }
                });

                //datepicker
                $('.datepicker').datepicker({
                    autoclose: true,
                    format: "dd-mm-yyyy",
                    todayHighlight: true,
                    orientation: "top auto",
                    todayBtn: true,
                    todayHighlight: true,  
                });
            });

            function add_person()
            {
                save_method = 'add';
                
                $('#form')[0].reset();
                $('.help-block').empty();
                $('#modal_form').modal('show');
                $('.form-group').removeClass('has-error');
                $('.modal-title').text('Adicionar usuário');
            }

            function edit_person(id)
            {
                save_method = 'update';

                $('#form')[0].reset();
                $('.help-block').empty();
                $('.form-group').removeClass('has-error');

                $.ajax({
                    url : "<?= site_url('persons/ajax_edit/') ?>/" + id,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data) {
                        $('[name="id"]').val(data.id);
                        $('[name="firstName"]').val(data.firstName);
                        $('[name="lastName"]').val(data.lastName);
                        $('[name="gender"]').val(data.gender);
                        $('[name="address"]').val(data.address);
                        $('[name="dob"]').datepicker('update',data.dob);
                        $('#modal_form').modal('show');
                        $('.modal-title').text('Edita usuário');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error.');
                    }
                });
            }

            function reload_table()
            {
                table.ajax.reload(null, false);
            }

            function save()
            {
                var url;
                $('#btnSave').text('salvando...');
                $('#btnSave').attr('disabled', true);

                if (save_method == 'add') {
                    url = "<?= site_url('persons/ajax_add') ?>";
                } else {
                    url = "<?= site_url('persons/ajax_update') ?>";
                }

                $.ajax({
                    url : url,
                    type: "POST",
                    dataType: "JSON",
                    data: $('#form').serialize(),
                    success: function(data) {
                        if (data.status) {
                            $('#modal_form').modal('hide');
                            reload_table();
                        }

                        $('#btnSave').text('save');
                        $('#btnSave').attr('disabled',false);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Error adding / update data');
                        $('#btnSave').text('salvo');
                        $('#btnSave').attr('disabled',false);
                    }
                });
            }

            function delete_person(id)
            {
                if (confirm('Tem certeza de que deseja excluir esses dados?')) {
                    $.ajax({
                        url : "<?= site_url('persons/ajax_delete') ?>/"+id,
                        type: "POST",
                        dataType: "JSON",
                        success: function(data) {
                            $('#modal_form').modal('hide');
                            reload_table();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('Error.');
                        }
                    });
                }
            }
        </script>

        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title">Usuário</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Nome</label>
                                    <div class="col-md-9">
                                        <input name="firstName" placeholder="Nome" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Sobrenome</label>
                                    <div class="col-md-9">
                                        <input name="lastName" placeholder="Sobrenome" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Sexo</label>
                                    <div class="col-md-9">
                                        <select name="gender" class="form-control">
                                            <option value="">--Selecione--</option>
                                            <option value="male">Masculino</option>
                                            <option value="female">Feminino</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Endereço</label>
                                    <div class="col-md-9">
                                        <textarea name="address" placeholder="Endereço" class="form-control"></textarea>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Data</label>
                                    <div class="col-md-9">
                                        <input name="dob" placeholder="dd-mm-yyyy" class="form-control datepicker" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Salvar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>