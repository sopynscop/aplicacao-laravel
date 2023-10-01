<?php

namespace App\Http\Controllers;
use App\Models\Aplicacao;
use Illuminate\Http\Request;

class AplicacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd('Hello, World!');
        $aplicacoes = Aplicacao::all();
        //dd($aplicacao);
        return view('aplicacoes.index', ['aplicacoes'=>$aplicacoes]);
    }

    /**
     * Show the form for creating a new resource.
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('aplicacoes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->check()) {
            $request->validate([
                'nome' => 'required',
                'descricao' => 'required',
                'link' => 'required',
            ]);

            $aplicacao = new Aplicacao;
            $aplicacao->nome = $request->nome;
            $aplicacao->descricao = $request->descricao;
            $aplicacao->link = $request->link;
            $aplicacao->usuario_id = auth()->user()->id; 
            $aplicacao->save();

            if ($request->hasFile('imagem')) {
                $imagem = $request->file('imagem');
                $imagemNome = time() . '.' . $imagem->getClientOriginalExtension();
                $imagem->move(public_path('imagens/aplicacoes'), $imagemNome);
                $aplicacao->imagem = $imagemNome;
                $aplicacao->save();
            }

    
            return redirect()->route('aplicacoes-index');
        } else {
            return 'Usuário não autenticado.';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    $aplicacao = Aplicacao::find($id);

    // Verificar se a aplicação existe
    if ($aplicacao) {
        // Retornar a view de exibição dos detalhes da aplicação, passando o objeto $aplicacao como parâmetro
        return view('aplicacoes.show', compact('aplicacao'));
    }

    // Caso a aplicação não seja encontrada, redirecionar para alguma página de erro ou índice
    return redirect()->route('aplicacoes-index');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $aplicacoes = Aplicacao::where('id',$id)->first();
        
        if(!empty($aplicacoes)){
            return view('aplicacoes.edit', ['aplicacoes'=>$aplicacoes]);
        } else {
            return redirect()->route('aplicacoes-index');
        }
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    $aplicacao = Aplicacao::find($id);
        $data = [
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'link' => $request->link,
        ];

        if ($request->hasFile('imagem')) {
            $imagem = $request->file('imagem');
            $imagemNome = time() . '.' . $imagem->getClientOriginalExtension();
            $imagem->move(public_path('imagens/aplicacoes'), $imagemNome);
            $data['imagem'] = $imagemNome;
        }

        Aplicacao::where('id', $id)->update($data);
        return redirect()->route('aplicacoes-index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //dd($id);
        Aplicacao::where('id', $id)->delete();
        return redirect()->route('aplicacoes-index');
    }
}
