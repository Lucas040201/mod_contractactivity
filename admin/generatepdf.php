<?php
global $CFG, $DB;

require_once('./../../../config.php');
require_once($CFG->libdir . '/pdflib.php');
require_login();
//require_capability('mod/contractactivity:generatetext', context_system::instance());

$submissionid = required_param('id', PARAM_INT);

// 🔹 Aqui você buscaria os dados do formulário do aluno
$submission = $DB->get_record('contractactivity_submissions', ['id' => $submissionid], '*', MUST_EXIST);
$course = get_course($submission->courseid);

$student_name      = $submission->fullname;
$cpf_document       = $submission->cpf;
$student_document        = $submission->document;
$nationality  = $submission->nationality;
$profession      = $submission->profession;
$address       = "{$submission->address}, {$submission->address_number} – {$submission->address_neighbourhood} – {$submission->address_city}, {$submission->postal_code}";
$course_name          = $course->fullname;
$today       = userdate(time(), '%d de %B de %Y');

$civil_status    = match($submission->civilstatus) {
    '0' => 'Solteiro(a)',
    '1' => 'Casado(a)',
    '2' => 'Divorciado(a)',
    '3' => 'Viúvo(a)',
    default => 'Estado civil Desconhecido'
};

$textoContrato = <<<HTML
<p style="text-align:center;"><b>CONTRATO DE PRESTAÇÃO DE SERVIÇOS EDUCACIONAIS – PÓS-GRADUAÇÃO LATO SENSU</b><br /><br /></p>
<p><b>QUALIFICAÇÃO DAS PARTES:</b><br /><br /></p>
<p style="text-align: justify;"><b>CONTRATANTE:</b> {$student_name}, {$nationality}, {$civil_status}, {$profession}, portador(a) da cédula de identidade R.G. nº {$student_document} e CPF/MF nº {$cpf_document}, residente e domiciliado(a) em {$address}.</p>

<p style="text-align: justify;"><b>CONTRATADA: ATC Soluções Educacionais Ltda. – ME</b>, com sede na Rua Salvador Cabral, 345 – Centro – Mogi das Cruzes – SP, inscrita no CNPJ sob o nº 65.014.535/0001-67, neste ato representada por <b>Leonardo Gonçalves Canalli</b>, brasileiro, empresário, portador da cédula de identidade R.G. nº 49.566.588-5 e CPF/MF nº 374.672.018-42.</p>

<p style="text-align: justify;">As partes firmam o presente contrato, que se regerá pela legislação vigente, especialmente o Código de Defesa do Consumidor, legislação educacional aplicável, normas do Ministério da Educação (MEC), bem como pelo Regimento Interno, Manual do Aluno, Edital do curso, políticas institucionais e demais regulamentos da CONTRATADA, que passam a integrar este instrumento independentemente de transcrição.<br /><br /></p>

<p style="text-align: justify;"><b>CLÁUSULA 1ª – DO OBJETO</b><br /><br /> 
O presente contrato tem por objeto a prestação de serviços educacionais na modalidade de ensino a distância (EAD), relativos ao curso de pós-graduação lato sensu contratado.
<br /><br />Parágrafo 1º: A prestação dos serviços observará o projeto pedagógico do curso, podendo incluir atividades síncronas e assíncronas, avaliações, trabalhos acadêmicos e demais exigências.
<br /><br />Parágrafo 2º: A CONTRATADA possui autonomia didático-pedagógica e administrativa, podendo, a qualquer tempo, alterar grade curricular, conteúdo programático, cronograma, corpo docente, metodologia, sistema de avaliação, carga horária e demais aspectos acadêmicos, mediante comunicação prévia ao CONTRATANTE. 
<br /><br />Parágrafo 3º: Todas as regras acadêmicas complementares constam nos documentos institucionais, sendo de responsabilidade do CONTRATANTE sua leitura e cumprimento.
<br /><br /></p>

<p><b>CLÁUSULA 2ª – DAS OBRIGAÇÕES DA CONTRATADA</b></p>
<p>A CONTRATADA compromete-se a:</p>
<p style="text-align: justify">I – disponibilizar o conteúdo acadêmico por meio de plataforma digital;<br />
II – oferecer suporte acadêmico dentro de prazo razoável;<br />
III – realizar avaliações e acompanhamento pedagógico;<br />
IV – disponibilizar orientação para TCC, quando aplicável;<br />
V – emitir certificado de conclusão ao aluno aprovado, conforme legislação vigente;<br />
VI – comunicar alterações relevantes.</p>
<p style="text-align: justify">Parágrafo único: A CONTRATADA não garante que os serviços serão ininterruptos, livres de falhas ou erros técnicos, não se responsabilizando por indisponibilidades decorrentes de fatores externos, casos fortuitos ou força maior. <br /><br /></p>

<p><b>CLÁUSULA 3ª – DAS OBRIGAÇÕES DO CONTRATANTE</b></p>
<p>O CONTRATANTE obriga-se a:</p>
<p style="text-align: justify">I – cumprir todas as normas institucionais;<br />
II – participar das atividades obrigatórias;<br />
III – obter aproveitamento mínimo exigido;<br />
IV – manter dados atualizados;<br />
V – zelar pela confidencialidade de login e senha;<br />
VI – não compartilhar acesso à plataforma;<br />
VII – não reproduzir, distribuir ou comercializar qualquer conteúdo do curso;<br />
VIII – possuir equipamentos, softwares e acesso à internet adequados;<br />
IX – responder por problemas técnicos decorrentes de seus próprios meios;<br />
X – efetuar pagamentos pontualmente</p>
<p style="text-align: justify">Parágrafo 1º: O CONTRATANTE é responsável por toda utilização de sua conta de acesso.</p>
<p style="text-align: justify">Parágrafo 2º: É vedado ao CONTRATANTE:<br />
a) violar sistemas da CONTRATADA;<br />
b) utilizar dados de terceiros;<br />
c) praticar atos ilícitos ou antiéticos no ambiente virtual;<br />
d) compartilhar conteúdo com terceiros;<br />
e) realizar engenharia reversa da plataforma.<br /><br />
</p>

<p><b>CLÁUSULA 4ª – DO PAGAMENTO</b></p>
<p style="text-align: justify">O CONTRATANTE pagará pelos serviços conforme condições definidas no ato da contratação.</p>
<p style="text-align: justify">Parágrafo 1º: O não recebimento de boleto não exime o pagamento, sendo responsabilidade do CONTRATANTE solicitar o boleto ao setor financeiro...</p>
<p style="text-align: justify">Parágrafo 2º: O inadimplemento por período igual ou superior a 90 dias poderá ensejar a rescisão contratual, bem como a cobrança judicial ou extrajudicial dos valores devidos, acrescidos de honorários advocatícios de até 20% e demais despesas.</p>
<p style="text-align: justify">Parágrafo 3º: O acesso ao curso, bem como a progressão para módulos subsequentes, poderá ser suspenso em caso de inadimplência.</p>

<p><b>CLÁUSULA 5ª – DA MATRÍCULA E DOCUMENTAÇÃO</b></p>
<p style="text-align: justify">A matrícula está condicionada à entrega da documentação exigida.</p>
<p style="text-align: justify">Parágrafo 1º: A ausência ou irregularidade documental poderá acarretar bloqueio de acesso ou cancelamento da matrícula.</p>
<p style="text-align: justify">Parágrafo 2º: A apresentação de diploma de graduação é condição indispensável para certificação.</p>
<p style="text-align: justify">Parágrafo 3º: A matrícula realizada com documentação irregular poderá ser cancelada a qualquer tempo.<br /><br /></p>

<p><b>CLÁUSULA 6ª – DO CANCELAMENTO E RESCISÃO</b></p>
<p style="text-align: justify">O CONTRATANTE poderá desistir:</p>
<p style="text-align: justify">I – no prazo de até 7 dias após o pagamento, com reembolso integral;<br /></p>
<p style="text-align: justify">Parágrafo 1º: O cancelamento deverá ser solicitado exclusivamente por meio dos canais oficiais da CONTRATADA.</p>
<p style="text-align: justify">Parágrafo 2º: A ausência de acesso não caracteriza cancelamento.</p>
<p style="text-align: justify">Parágrafo 3º: Valores já vencidos permanecem devidos.<br /><br /></p>

<p><b>CLÁUSULA 7ª – DO PRAZO E PROGRESSÃO</b></p>
<p style="text-align: justify">O curso terá prazo mínimo e máximo definidos na contratação</p>
<p style="text-align: justify">Parágrafo 1º: O não cumprimento dos requisitos acadêmicos ou financeiros poderá impedir a progressão no curso.</p>
<p style="text-align: justify">Parágrafo 2º: A prorrogação poderá ser concedida mediante pagamento adicional previamente informado.<br /><br /></p>

<p><b>CLÁUSULA 8ª – DOS SERVIÇOS ADICIONAIS</b></p>
<p style="text-align: justify">Não estão incluídos neste contrato serviços como:</p>
<p style="text-align: justify">– prorrogações<br />
– atividades extracurriculares<br /><br />
</p>

<p><b>CLÁUSULA 9ª – DA PROPRIEDADE INTELECTUAL</b></p>
<p style="text-align: justify">Todo conteúdo disponibilizado é protegido por lei, sendo vedada sua reprodução, distribuição ou utilização indevida, sob pena de responsabilização civil e criminal.<br/><br/></p>

<p><b>CLÁUSULA 10ª – DA DISPONIBILIDADE E LIMITAÇÃO DE RESPONSABILIDADE</b></p>
<p style="text-align: justify">A CONTRATADA não garante disponibilidade contínua da plataforma, ausência de falhas ou compatibilidade com todos os dispositivos.</p>
<p style="text-align: justify">Parágrafo único: Problemas técnicos externos, incluindo falhas de internet ou equipamentos do CONTRATANTE, não são de responsabilidade da CONTRATADA.<br/><br/></p>

<p><b>CLÁUSULA 11ª – DA CESSÃO E TRANSFERÊNCIA</b></p>
<p style="text-align: justify">A CONTRATADA poderá ceder ou transferir o presente contrato a terceiros, independentemente de autorização prévia.<br /><br /></p>

<p><b>CLÁUSULA 12ª – DISPOSIÇÕES GERAIS</b></p>
<p style="text-align: justify">I – O presente contrato constitui título executivo extrajudicial;<br />
II – Todas as solicitações deverão ser feitas pelos canais oficiais da CONTRATADA;<br />
III – O CONTRATANTE declara ter lido, compreendido e aceitado integralmente este contrato;<br />
IV – Declara ainda que não possui dúvidas quanto às cláusulas;<br />
V – Eventuais tolerâncias não implicam renúncia de direitos.<br /><br />
</p>

<p><b>CLÁUSULA 13ª – DO FORO</b></p>
<p style="text-align: justify">Fica eleito o foro da Comarca de Mogi das Cruzes/SP para dirimir quaisquer controvérsias oriundas deste contrato.<br /><br /><br /><br /><br /></p>


<p><b>Mogi das Cruzes, {$today}</b><br /><br /></p>

<p><b>CONTRATANTE</b><br />{$student_name}<br /><br /></p>
<p><b>CONTRATADA</b><br />ATC Soluções Educacionais Ltda. – ME<br />Leonardo Gonçalves Canalli</p>
HTML;


$pdf = new \pdf();
$margin_in_mm = 25.4;
$pdf->setMargins($margin_in_mm, $margin_in_mm, $margin_in_mm);
$pdf->setAutoPageBreak(true, $margin_in_mm);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$pdf->writeHTML($textoContrato);
$pdf->Output('Contrato_'.$student_name.'.pdf', 'I');
exit;
