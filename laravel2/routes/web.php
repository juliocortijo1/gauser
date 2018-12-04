<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
//Rotas que exigem autenticação
Route::group(['middleware'=>'auth'],function() {
    //Fornecedor
    Route::resource('fornecedor', 'FornecedorController');
    Route::post('/fornecedor/searchFor', 'FornecedorController@searchFor')->name('fornecedor.searchFor');
    Route::get('/fornecedor/search', 'FornecedorController@search')->name('fornecedor.search');
    //Contratos

    Route::resource('contratos', 'ContratosController');
    Route::get('contratos/', 'ContratosController@index')->name('contratos.index');
    Route::post('contratos/delete', 'ContratosController@delete')->name('contratos.delete');
    Route::post('contratos/update', 'ContratosController@update')->name('contratos.update');
    Route::post('contratos/searchFor', 'ContratosController@searchFor')->name('contratos.searchFor');
    Route::get('contratos/search', 'ContratosController@search')->name('contratos.search');
    Route::post('contratos/searchForToExtrato', 'ContratosController@searchForToExtrato')->name('contratos.searchForToExtrato');
    //Usuarios
    Route::get('usuarios/', 'UsuarioController@index')->name('usuarios.index');
    Route::get('usuarios/create', 'UsuarioController@create')->name('usuarios.create');
    Route::post('usuarios/createnew', 'UsuarioController@CreateNew')->name('usuarios.CreateNew');
    Route::post('usuarios/searchFor', 'UsuarioController@searchFor')->name('usuarios.searchFor');
    Route::get('usuarios/search', 'UsuarioController@search')->name('usuarios.search');
    Route::get('usuarios/list', 'UsuarioController@list')->name('usuarios.list');

    Route::get('usuarios/me', 'UsuarioController@SefEdit')->name('usuarios.me');
    Route::post('usuarios/updateme', 'UsuarioController@UpdateMe')->name('usuarios.updateme');

    Route::get('usuarios/assinatura', 'AssinaturaConfiguracoesController@index')->name('usuarios.assinatura');
    Route::post('usuarios/assinatura/enviar', 'AssinaturaConfiguracoesController@enviar')->name('usuarios.assinaturaEnviar');

    Route::post('usuarios/delete', 'UsuarioController@delete')->name('usuarios.delete');
    Route::get('usuarios/{id}/edit', 'UsuarioController@edit')->name('usuarios.edit');
    Route::post('usuarios/update', 'UsuarioController@update')->name('usuarios.update');

//Fim Routes usuario



//Secretarias
    Route::get('secretarias/', 'SecretariaController@index')->name('secretaria.index');
    Route::get('secretarias/create', 'SecretariaController@create')->name('secretaria.create');
    Route::post('secretarias/store', 'SecretariaController@store')->name('secretaria.store');
    Route::post('secretarias/delete', 'SecretariaController@delete')->name('secretaria.delete');
    Route::get('secretarias/{id}/edit', 'SecretariaController@edit')->name('secretaria.edit');
    Route::post('secretarias/update', 'SecretariaController@update')->name('secretaria.update');
    Route::get('secretarias/search', 'SecretariaController@search')->name('secretaria.search');
    Route::post('secretarias/searchFor', 'SecretariaController@searchFor')->name('secretaria.searchFor');
    Route::post('secretarias/searchForassign', 'SecretariaController@searchForassign')->name('secretaria.searchForassign');

    Route::post('secretarias/assignSecUser', 'SecretariaController@assignSecUser')->name('secretaria.assignSecUser');
    Route::post('secretarias/DeassignSecUser', 'SecretariaController@DeassignSecUser')->name('secretaria.DeassignSecUser');
    Route::get('secretarias/{id}/assign', 'SecretariaController@assign')->name('secretaria.assign');
//Fim Routes Secretaria



    //Contas

    Route::get('contas/create', 'ContratosController@CreateExtratoContrato')->name('contas.create');
    Route::post('contas/store', 'ContasController@store')->name('contas.store');
    Route::get('contas/{id}/createnew', 'ContasController@createExtrato')->name('contas.createnew');
    Route::delete('contasdeleteall', 'ContasController@deleteAll')->name('contas.deleteall');
    Route::post('contasaproveAll', 'ContasController@aproveAll')->name('contas.aproveAll');
    Route::post('contas/searchFor', 'ContasController@searchFor')->name('conta.searchFor');
    Route::post('contas/searchForall', 'ContasController@searchForall')->name('conta.searchForall');
    Route::get('contas/searchForall', 'ContasController@contasContratoall')->name('conta.searchForall');
    Route::get('contas/searchFor', 'ContratosController@CreateExtratoContrato')->name('conta.searchFor');
    Route::get('contas/{id}/{id_contrato}/aprove', 'ContasController@aprove')->name('contas.aprove');
    Route::get('contas/{id}/{id_contrato}/reeopen', 'ContasController@reeopen')->name('contas.reeopen');
    Route::get('contas/{id}/{id_contrato}/invalidate', 'ContasController@invalidate')->name('contas.invalidate');
    Route::get('contas/{id}/contasContratoId', 'ContasController@contasContratoId')->name('contas.contasContratoId');
    Route::get('contas/{id}/{id_contrato}/contasContratoContestaId', 'ContasController@contasContratoContestaId')->name('contas.contasContratoContestaId');
    Route::post('contas/contestacaoEnviar','ContasController@contestacaoEnviar')->name('contas.contestacaoEnviar');
    Route::get('contas/{tipo?}/contasContratoall', 'ContasController@contasContratoall')->name('contas.contasContratoall');
    Route::get('contas/{conta_id}/gerapdf', 'AssinaturaConfiguracoesController@Create_PDF_Aprove')->name('contas.geradpf');
    Route::get('usuarios/assinatura', 'AssinaturaConfiguracoesController@index')->name('usuarios.assinatura');
    Route::post('usuarios/assinatura/enviar', 'AssinaturaConfiguracoesController@enviar')->name('usuarios.assinaturaEnviar');
    Route::get('token/valida/', 'TokenController@valida')->name('token.valida');
    Route::get('/', 'ContratosController@CreateExtratoContrato')->name('index.home');
    Route::get('/home', 'ContratosController@CreateExtratoContrato')->name('home');
});