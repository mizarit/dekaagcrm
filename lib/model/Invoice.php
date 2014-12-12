<?php

class DeKaagInvoice extends DeKaagBase {
  public $model = 'invoice';
  
  public $relations = array(
    'rows' => array('invoice_row', 'id'),
    'relation' => 'relation_id',
    'persona' => 'persona_id'
  );
  
  public static function model()
  {
    return new DeKaagInvoice;
  }
  
  public function getRelationStr()
  {
    return $this->relation->title;
  }
  
  public function getCompanyStr()
  {
    return $this->company == 2 ? 'Spaarnwoude' : 'De Kaag';
  }
  
  public function getStatusStr()
  {
    $status[0] = __('unknown', 'dekaagcrm');
    $status[1] = __('pending', 'dekaagcrm');
    $status[2] = __('open', 'dekaagcrm');
    $status[3] = __('payed_partial', 'dekaagcrm');
    $status[4] = __('payed', 'dekaagcrm');
    $status[5] = __('credit', 'dekaagcrm');
    
    return $status[$this->status];
  }
  
  public function getTotalStr($formatted = true)
  {
    $rows = $this->rows;
    $total = 0;
    foreach ($rows as $row) {
      $total += $row->total;
    }
    return $formatted ? '€ '.number_format($total,2,',','.') : $total;
  }
  
  public function getTotalRemainingStr($formatted = true)
  {
    $total = $this->getTotalStr(false);
    $payments = DeKaagPayment::model()->findAllByAttributes(new DeKaagCriteria(array(
      $this->prefix().'invoice_id' => $this->id,
       'status' => 'success'
    )));
    foreach ($payments as $payment) {
      $total -= $payment->total;
    }
    if ($total < 0) $total = 0;
    
    return $formatted ? '€ '.number_format($total,2,',','.') : $total;
  }
  
  public function getPayedStr($formatted = true)
  {
    $total = $this->getTotalStr(false);
    $remaining = $this->getTotalRemainingStr(false);
    $total = $total - $remaining;
    return $formatted ? '€ '.number_format($total,2,',','.') : $total;
  }
  
  public function save()
  {
    
    if ($this->invoicenr == '' && isset($_POST['send_invoice'])) {
      $total = DeKaagInvoice::model()->count(new DeKaagCriteria(array(
        'date' => array('2014%', 'LIKE'),
        'status' => array(array(2,3,4,5), 'IN')
      )));
      $invoicenr = 'ZDK'.date('y').str_pad(501+$total, 4, '0', STR_PAD_LEFT);
      $this->invoicenr = $invoicenr;
      if ($this->status < 2) {
        $this->status = 2;
      }
    }
    
    if ($this->status == 0) {
      $this->status = 1;// set it to pending by default'
    } 
    
    parent::save();
    
    if (isset($_POST['send_invoice'])) {
      $options = get_option('dekaagcrm_plugin_options');
      $sender_name = isset($options['plugin_sender_name']) ? $options['plugin_sender_name'] : 'De Kaag Watersport';

      $vars = array(
        'title' => $this->relation->title,
        'sender_name' => $sender_name,
        'date' => date('d-m-Y', strtotime($this->date)),
        'end_date' => date('d-m-Y', strtotime($this->enddate)),
        'invoicenr' => $this->invoicenr,
        'total' => $this->getTotalStr(false),
        'downpayment' => $this->downpayment != 'none' ? ($this->downpayment == 'fixed' ? $this->dpvalue : ($this->getTotalStr(false)/100)*round($this->dpvalue) ) : false
      );
      
      $filename = $this->invoicenr.'.pdf';
    
      DeKaagInvoice::generate_pdf($filename, $this);
    
      $attachment = dirname(__FILE__).'/../../data/'.$filename;
      	    
	    DeKaagCRM_Admin::send_mail(
	       $this->relation->email,
	       __('Invoice', 'dekaagcrm'),
	       'invoice',
	       $vars,
	       array(
	         $attachment
	       )
	    );
    }
  }
  
  public function findByCode($code)
  {
    return DeKaagInvoice::model()->findByAttributes(new DeKaagCriteria(array('invoicenr' => $code)));
  }
  
  public static function generate_pdf($filename, $model)
	{
	  $rows = $model->rows;
	  $options = get_option('dekaagcrm_plugin_options');
	  $account_no = isset($options['plugin_accountno']) ? $options['plugin_accountno'] : 'NL08 RABO 0102575568';
	  $account_holder = isset($options['plugin_accountholder']) ? $options['plugin_accountholder'] : 'De Kaag Watersport';
	  $account_iban = isset($options['plugin_iban']) ? $options['plugin_iban'] : 'NL08 RABO 0102575568';
	  $account_btw = isset($options['plugin_btw']) ? $options['plugin_btw'] : '813666181B01';
	  $account_kvk = isset($options['plugin_kvk']) ? $options['plugin_kvk'] : '28102735';
	  $account_site = isset($options['plugin_site']) ? $options['plugin_site'] : 'http://www.dekaag.nl/';
	  $account_email = isset($options['plugin_email']) ? $options['plugin_email'] : 'info@dekaag.nl';
	  
	  $logo_path = realpath(dirname(__FILE__).'/../../data').'/logo-dekaag-invoice.jpg';

	  $rows = array();
    $total = 0;
    foreach ($model->rows as $row_data) {
      $total += $row_data->total;
    }
    
    require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/vendor/fpdf/PDF.php' );
    $pdf= new PDF();
    $pdf->AddPage();
    $pdf->AddFont('Futura');
    $pdf->AddFont('Futura', 'B');
    $pdf->SetFont('Futura','',14);
    $pdf->SetRightMargin(0);
    $pdf->SetFillColor(247,247,247);
    $pdf->Rect(0,0,220,28, 'F');
    $pdf->Image($logo_path,10,5, 50);
    
    $pdf->setY(5);
    $pdf->setX(100);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor(0,165,226);
    $pdf->Write(4, $account_holder);
    $pdf->Ln(4);
    $pdf->SetFontSize(8);
    $pdf->SetTextColor(33,33,33);
    $pdf->setX(100);
    $pdf->Write(5, sprintf('%s | %s', $account_site, $account_email));
    $pdf->Ln(4);
    $pdf->setX(100);
    $pdf->Write(5, sprintf('KvK %s | BTW %s | IBAN %s', $account_kvk, $account_btw, $account_iban));
    $pdf->Ln(8);
    $pdf->setX(100);
    $pdf->SetTextColor(0,165,226);
    $pdf->SetFontSize(14);
    $pdf->SetStyle('B',true);
    $pdf->Write(5, $total > 0 ? 'FACTUUR' : 'CREDITNOTA');
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);
      
    $pdf->SetStyle('B',true);
    $pdf->SetTextColor(0,165,226);
        
    $offset = 0;
  
    $pdf->Ln(15);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor(33,33,33);
    $pdf->Write(5, 'Uw gegevens');
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);
    $parts = explode("\n",$model->relation->title.PHP_EOL.$model->address);
    if (count($parts) < 5) {
      for($c = count($parts); $c < 5; $c++) {
        $parts[] = '';
      }
    }
    $parts = array_slice($parts,0,3);
    foreach ($parts as $part) {
      $pdf->Write(5, html_entity_decode($part));
      $pdf->Ln(5);
    }
    $offset += ((count($parts) - 3) * 5);
    
    $pdf->Ln(5);  
    
    $pdf->SetStyle('B',true);
    $pdf->Write(5, 'Kenmerken');
    $pdf->Ln(5);
    $pdf->SetStyle('B',false);
    
    $pdf->Write(5, 'Factuurdatum');
    $pdf->Write(5, '');
    $pdf->SetX(50);
    $pdf->Write(5, date('d-m-Y', strtotime($model->date)));
    $pdf->Write(5, '');
    $pdf->Ln(5);
    $pdf->Write(5, 'Factuurnummer');
    $pdf->SetX(50);
    
    $pdf->Write(5, $model->invoicenr);
    
    $pdf->Ln(10);
    
    $pdf->SetStyle('B',true);
    $pdf->SetTextColor(0,165,226);
    $pdf->Write(5, 'Omschrijving');
    $pdf->SetX(160);
    $pdf->Write(5, 'BTW');
    $pdf->SetX(180);
    $pdf->Write(5, 'Totaal');
    $pdf->Ln(10);
               
    $pdf->SetLineWidth(0.3);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,93+$offset,200,93+$offset);
    
    $pdf->SetTextColor(33,33,33);
    $pdf->SetStyle('B',false);
    
    $rows = array();
    $total = 0;
    $vats = array();
    foreach ($model->rows as $row_data) {
      $total += $row_data->total;
      $v = ($row_data->total / (100+$row_data->vat)) * $row_data->vat;
      if (!isset($vats[$row_data->vat])) $vats[$row_data->vat] = 0;
      $vats[$row_data->vat] += $v; 
      $row = array(
        substr($row_data->description, 0, 80), 
        ($row_data->vat != 0 && $row_data->total != 0 ) ? $row_data->vat.'%' : '',
        $row_data->total != 0 ? '€ '.str_replace(',00', ',-', number_format($row_data->total, 2, ',', '.')) : '',
      );
      $rows[] = $row;
    }
    
    for ($c = count($rows); $c < (24 - ($offset/5)); $c++)
    {
      $rows[] = array('', '');
    }
    
    foreach ($rows as $row) {
      $pdf->SetFont('Futura','',10);
      $pdf->Write(5, $row[0]);
      $pdf->SetX(160);
      $pdf->SetFont('Arial','',10);
      $pdf->Write(5, $row[1]);
      $pdf->SetX(180);
      $pdf->SetFont('Arial','',10);
      $pdf->Write(5, $row[2]);
      $pdf->SetFont('Futura','',10);
      $pdf->Ln(5);
    }
  
    $pdf->SetLineWidth(0.3);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,218,200,218);
    $pdf->Ln(5);
    
    $vat = strtotime($model->enddate) < strtotime(date('2012-10-01')) && strtotime($model->enddate) > 0 ? 19 : 21;
    $vat_factor = (100 + $vat) / 100;
  
    $total_ex = ($total / $vat_factor) * 1;
    
    $tv = 0;
    foreach ($vats as $v) {
      $tv += round($v, 2);
    }
    $tv = round($tv, 2);
    
    
    $pdf->SetX(140);    
    $pdf->Write(5, 'Totaal exclusief BTW');
    $pdf->SetX(180);
    $pdf->SetFont('Arial','',10);
    $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format($total - $tv, 2, ',', '.')));
    $pdf->SetFont('Futura','',10);
    $pdf->Ln(5);
    
    foreach ($vats as $vt => $v) {
      $pdf->SetX(140);
      $pdf->Write(5, $vt.'% BTW');
      $pdf->SetX(180);
      $pdf->SetFont('Arial','',10);
      $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format(round($v, 2) , 2, ',', '.')));
      $pdf->SetFont('Futura','',10);
      $pdf->Ln(5);
    } 
    
    $pdf->SetLineWidth(0.5);
    $pdf->SetDrawColor(178,178,178);
    $pdf->Line(10,(228+count($vats)*5),200,(228+count($vats)*5));
    $pdf->Ln(5);
    
  
    $pdf->SetStyle('B',true);
    $pdf->SetFontSize(12);
    $pdf->SetTextColor(0,165,226);
    $pdf->SetX(140);
    $pdf->Write(5, 'Totaal');
    $pdf->SetX(180);
    $pdf->SetFont('Arial','',12);
    $pdf->SetStyle('B',true);
    $pdf->Write(5, '€ '.str_replace(',00', ',-', number_format($total, 2, ',', '.')));
    $pdf->SetFont('Futura','',10);
    $pdf->Ln(5);
      
    $pdf->SetFont('Arial','',10);
    $pdf->SetStyle('B',false);
    $pdf->SetStyle('B',false);
    $pdf->SetFontSize(10);
    $pdf->SetTextColor(33,33,33);
    $pdf->Ln(18);
    $pdf->SetX(30);
    // 94 = 30 
    // 57 = 60
    // 91 = 33
   // echo strlen('Wij verzoeken u vriendelijk het volledige bedrag voor '.date('d-m-Y', strtotime($model->enddate)).' over te maken op bankrekening');
   // echo ' ';
   // echo strlen(sprintf('%s tnv %s o.v.v. het factuurnummer.', $account_no, $account_holder));
   // echo ' ';
   // echo strlen(sprintf('U kunt een aanbetaling dpen van € %s voor %s om de reservering te bevestigen.', number_format($model->dpvalue,2,',','.'), date('d-m-Y', strtotime($model->dpdate))));
   // exit;
    if ($total > 0) {
      $pdf->Write(5, 'Wij verzoeken u vriendelijk het volledige bedrag voor '.date('d-m-Y', strtotime($model->enddate)).' over te maken op bankrekening');
      $pdf->Ln(5);
      $pdf->SetX(50);
      $pdf->Write(2, sprintf('%s tnv %s o.v.v. het factuurnummer.', $account_no, $account_holder));
      $pdf->Ln(5);
      	
      if ($model->downpayment == 'fixed') {
        $pdf->SetX(33);
        $pdf->Write(5, sprintf('U kunt een aanbetaling dpen van € %s voor %s om de reservering te bevestigen.', number_format($model->dpvalue,2,',','.'), date('d-m-Y', strtotime($model->dpdate))));
        $pdf->Ln(5);
      }
      if ($model->downpayment == 'percent') {
        $pdf->SetX(29);
        $pdf->Write(5, sprintf('U kunt een aanbetaling doen van € %s ( %s%% ) voor %s om de reservering te bevestigen.', number_format(($total/100)*round($model->dpvalue),2,',','.'), round($model->dpvalue), date('d-m-Y', strtotime($model->dpdate))));
        $pdf->Ln(5);
      }
    }
    $path = dirname(__FILE__).'/../../data/'.$filename;
    $pdf->Output($path);
	}
}