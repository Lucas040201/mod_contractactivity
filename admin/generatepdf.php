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

<p style="text-align: justify;"><b>CONTRATADA:</b> ATC – ESCOLA DE AVIAÇÃO CIVIL LTDA, com sede na Rua Salvador Cabral, 345 – Centro – Mogi das Cruzes – SP, inscrita no CNPJ sob o nº 23.903.893/0001-80, neste ato representada por seu Diretor Executivo Leonardo Gonçalves Canalli.</p>

<p style="text-align: justify;">As partes acima qualificadas firmam o presente <b>Contrato de Prestação de Serviços Educacionais</b>, que se regerá pelas cláusulas e condições seguintes:<br /><br /></p>

<p style="text-align: justify;"><b>DO OBJETO</b><br /><br />
<b>Cláusula 1ª.</b> O objeto deste contrato é a prestação, pela CONTRATADA, dos serviços educacionais referentes ao curso de <b>Pós-Graduação Lato Sensu</b> {$course_name} escolhido pelo CONTRATANTE, em formato 100% EAD, conforme estrutura acadêmica aprovada e divulgada pela CONTRATADA.<br /><br /></p>

<p><b>DAS OBRIGAÇÕES DA CONTRATADA</b></p>
<p><b>Cláusula 2ª.</b> A CONTRATADA compromete-se a:</p>
<p style="text-align: justify"> I – disponibilizar os conteúdos acadêmicos (videoaulas, apostilas, atividades, fóruns, provas, tutoria e suporte);</p>
<p style="text-align: justify"> II – realizar as disciplinas síncronas (PBL) via plataforma online, em calendário previamente divulgado;</p>
<p style="text-align: justify"> III – designar orientador para a disciplina de TCC e oferecer banca avaliadora em datas estabelecidas;</p>
<p style="text-align: justify"> IV – fornecer suporte acadêmico via WhatsApp, e-mail e telefone, dentro do prazo de resposta razoável;</p>
<p style="text-align: justify"> V – emitir certificado de conclusão ao aluno aprovado, em conformidade com a legislação do MEC, em formato digital (podendo ser impressa mediante taxa adicional).</p>

<p><b>DAS OBRIGAÇÕES DO CONTRATANTE</b></p>
<p><b>Cláusula 3ª.</b> O CONTRATANTE deve:</p>
<p style="text-align: justify"> I – respeitar o Regulamento Acadêmico da CONTRATADA e as regras da pós-graduação, inclusive prazos de entrega de atividades e participação obrigatória nas disciplinas síncronas;</p>
<p style="text-align: justify"> II – participar integralmente das disciplinas síncronas (PBL), sendo a ausência em qualquer encontro causa de reprovação na disciplina;</p>
<p style="text-align: justify"> III – realizar todas as avaliações previstas, inclusive o TCC, com nota mínima 7,0 (sete);</p>
<p style="text-align: justify"> IV – entregar, no prazo de 10 (dez) dias a contar do primeiro pagamento, a documentação exigida (RG ou documento equivalente, comprovante de residência e diploma ou certidão de colação de grau em curso superior).</p>
<p style="text-align: justify"><b>Parágrafo Primeiro:</b> Caso a documentação exigida não seja entregue dentro do prazo estipulado, o acesso ao curso será bloqueado, impossibilitando a continuidade dos estudos até a regularização da pendência.</p>
<p style="text-align: justify"><b>Parágrafo Segundo:</b> A apresentação do diploma de curso superior é condição indispensável para a emissão do certificado de conclusão da pós-graduação.<br /><br /><br /></p>

<p><b>DO PAGAMENTO</b></p>
<p style="text-align: justify"><b>Cláusula 4ª.</b> O CONTRATANTE obriga-se a efetuar os pagamentos conforme valores, prazos e condições definidos no ato da matrícula ou em negociação formal com a CONTRATADA.<br /><br /></p>

<p><b>DO INADIMPLEMENTO</b></p>
<p style="text-align: justify"><b>Cláusula 5ª.</b> O atraso no pagamento implicará na aplicação de multa de 2% sobre a parcela em aberto, acrescida de juros de 1% ao mês e correção monetária.<br /><br /><br /></p>

<p><b>DA RECISÃO</b></p>
<p style="text-align: justify"><b>Cláusula 6ª.</b> O CONTRATANTE poderá rescindir este contrato no prazo de até 07 (sete) dias corridos após o primeiro pagamento, com direito ao reembolso integral dos valores pagos (direito de arrependimento previsto no CDC).<br /></p>
<p style="text-align: justify"><b>Cláusula 7ª.</b> Após o prazo mencionado, caso o aluno opte por rescindir o contrato:</p>
<p style="text-align: justify"> I – serão mantidos os pagamentos vencidos;</p>
<p style="text-align: justify"> II – os valores pagos não são devolvidos.<br /><br /></p>

<p><b>DO PRAZO</b></p>
<p style="text-align: justify"><b>Cláusula 8ª.</b> O prazo mínimo para conclusão do curso é de 06 (seis) meses e o máximo de 12 (doze) meses, contados a partir da data do primeiro pagamento.</p>
<p style="text-align: justify">Parágrafo primeiro: caso não conclua dentro do prazo, o CONTRATANTE poderá solicitar extensão de acesso por mais 12 meses, mediante pagamento de taxa adicional correspondente a aproximadamente 30% do valor do curso atualizado.</p>
<p style="text-align: justify">Parágrafo segundo: o não cumprimento do prazo máximo sem solicitação formal implicará em desligamento automático, sem direito a ressarcimento.<br /><br /></p>

<p><b>DAS CONDIÇÕES ACADÊMICAS</b></p>
<p style="text-align: justify"><b>Cláusula 9ª.</b> Para aprovação, o aluno deve:</p>
<p style="text-align: justify">I – obter média mínima 7,0 em todas as disciplinas;</p>
<p style="text-align: justify">II – ser aprovado em todas as avaliações e cumprir presença nas disciplinas síncronas;</p>
<p style="text-align: justify">II – ser aprovado em todas as avaliações e cumprir presença nas disciplinas síncronas;</p>
<p style="text-align: justify">III – concluir e ser aprovado no TCC perante banca avaliadora.<br /><br /></p>

<p><b>DAS ATIVIDADES COMPLEMENTARES</b></p>
<p style="text-align: justify"><b>Cláusula 10ª.</b>O aluno terá acesso a workshops, palestras e cursos de extensão promovidos pela CONTRATADA, de caráter opcional, sem substituição das disciplinas obrigatórias.<br /><br /></p>


<p><b>DO USO DE IMAGEM</b></p>
<p style="text-align: justify"><b>Cláusula 11ª.</b>O CONTRATANTE autoriza a utilização de sua imagem e voz durante atividades síncronas e/ou presenciais, exclusivamente para fins acadêmicos, comerciais e institucionais da CONTRATADA.<br /><br /></p>


<p><b>DO FORO</b></p>
<p style="text-align: justify"><b>Cláusula 12ª.</b>Fica eleito o foro da Comarca de Mogi das Cruzes/SP para dirimir quaisquer controvérsias oriundas deste contrato.<br /><br /><br /></p>



<p><b>Mogi das Cruzes, {$today}</b><br /><br /></p>

<p><b>CONTRATANTE</b><br />{$student_name}<br /><br /></p>
<p><b>CONTRATADA</b><br />ATC – Faculdade de Aviação Civil<br />Leonardo Gonçalves Canalli – Diretor Executivo</p>
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
