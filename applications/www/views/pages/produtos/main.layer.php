
<div> HUE Meus produtos - {{ echo $this->request->a; }}</div>

<br/>

{{ print_r($this->Produtos->getitem()['name']); }}

<br/><br/>

Loop de arrays:<br/>

{{ foreach($this->request->gets as $item){ }}

{{ if($item != ""){ }}

<div>
    {{ echo $item; }}<br/>
</div>

{{ } }}

{{ } }}

<br/><br/>

Loop com sintaxe for:<br/>

{{ for($i = 0; $i < 5; $i++){ }}

<div>
    {{ echo $i; }}<br/>
</div>

{{ } }}

<br/><br/>

{{ echo $this->functions->caseDate(); }}

Verificação, condição if:<br/>

{{ if($this->request->b == "main"){ }}

<div>
    {{ echo $this->functions->encodeCrypt($this->request->b); }}<br/>
    {{ echo "Contém o parametro 'main' na url"; }}
</div>

{{ }else{ }}

<div>
    {{ echo "Nao contém o parametro 'main' na url."; }}
</div>

{{ } }}

<br/><br/>

{{ $data = "2016-11-10"; }}
{{ $dataday = "2016-11-18"; }}

{{ $diferenca = strtotime($dataday) - strtotime($data); }}
{{ $dias = floor($diferenca / (60 * 60 * 24)); }}

{{ echo $dias; }}

<br/><br/>

Base URL: {{baseurl}}<br/>
Local URL: {{local}}<br/>
