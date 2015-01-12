<?php

/**
 * Schema object for: InvoiceAddRq
 * 
 * @author "Keith Palmer Jr." <Keith@ConsoliByte.com>
 * @license LICENSE.txt
 * 
 * @package QuickBooks
 * @subpackage QBXML
 */

/**
 * 
 */
require_once 'QuickBooks.php';

/**
 * 
 */
require_once 'QuickBooks/QBXML/Schema/Object.php';

/**
 * 
 */
class QuickBooks_QBXML_Schema_Object_InvoiceAddRq extends QuickBooks_QBXML_Schema_Object
{
	protected function &_qbxmlWrapper()
	{
		static $wrapper = 'InvoiceAdd';
		
		return $wrapper;
	}
	
	protected function &_dataTypePaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 'IDTYPE',
  'CustomerRef FullName' => 'STRTYPE',
  'ClassRef ListID' => 'IDTYPE',
  'ClassRef FullName' => 'STRTYPE',
  'ARAccountRef ListID' => 'IDTYPE',
  'ARAccountRef FullName' => 'STRTYPE',
  'TemplateRef ListID' => 'IDTYPE',
  'TemplateRef FullName' => 'STRTYPE',
  'TxnDate' => 'DATETYPE',
  'RefNumber' => 'STRTYPE',
  'BillAddress Addr1' => 'STRTYPE',
  'BillAddress Addr2' => 'STRTYPE',
  'BillAddress Addr3' => 'STRTYPE',
  'BillAddress Addr4' => 'STRTYPE',
  'BillAddress Addr5' => 'STRTYPE',
  'BillAddress City' => 'STRTYPE',
  'BillAddress State' => 'STRTYPE',
  'BillAddress PostalCode' => 'STRTYPE',
  'BillAddress Country' => 'STRTYPE',
  'BillAddress Note' => 'STRTYPE',
  'ShipAddress Addr1' => 'STRTYPE',
  'ShipAddress Addr2' => 'STRTYPE',
  'ShipAddress Addr3' => 'STRTYPE',
  'ShipAddress Addr4' => 'STRTYPE',
  'ShipAddress Addr5' => 'STRTYPE',
  'ShipAddress City' => 'STRTYPE',
  'ShipAddress State' => 'STRTYPE',
  'ShipAddress PostalCode' => 'STRTYPE',
  'ShipAddress Country' => 'STRTYPE',
  'ShipAddress Note' => 'STRTYPE',
  'IsPending' => 'BOOLTYPE',
  'PONumber' => 'STRTYPE',
  'TermsRef ListID' => 'IDTYPE',
  'TermsRef FullName' => 'STRTYPE',
  'DueDate' => 'DATETYPE',
  'SalesRepRef ListID' => 'IDTYPE',
  'SalesRepRef FullName' => 'STRTYPE',
  'FOB' => 'STRTYPE',
  'ShipDate' => 'DATETYPE',
  'ShipMethodRef ListID' => 'IDTYPE',
  'ShipMethodRef FullName' => 'STRTYPE',
  'ItemSalesTaxRef ListID' => 'IDTYPE',
  'ItemSalesTaxRef FullName' => 'STRTYPE',
  'Memo' => 'STRTYPE',
  'CustomerMsgRef ListID' => 'IDTYPE',
  'CustomerMsgRef FullName' => 'STRTYPE',
  'IsToBePrinted' => 'BOOLTYPE',
  'IsToBeEmailed' => 'BOOLTYPE',
  'IsTaxIncluded' => 'BOOLTYPE',
  'CustomerSalesTaxCodeRef ListID' => 'IDTYPE',
  'CustomerSalesTaxCodeRef FullName' => 'STRTYPE',
  'Other' => 'STRTYPE',
  'LinkToTxnID' => 'IDTYPE',
  'InvoiceLineAdd ItemRef ListID' => 'IDTYPE',
  'InvoiceLineAdd ItemRef FullName' => 'STRTYPE',
  'InvoiceLineAdd Desc' => 'STRTYPE',
  'InvoiceLineAdd Quantity' => 'QUANTYPE',
  'InvoiceLineAdd UnitOfMeasure' => 'STRTYPE',
  'InvoiceLineAdd Rate' => 'PRICETYPE',
  'InvoiceLineAdd RatePercent' => 'PERCENTTYPE',
  'InvoiceLineAdd PriceLevelRef ListID' => 'IDTYPE',
  'InvoiceLineAdd PriceLevelRef FullName' => 'STRTYPE',
  'InvoiceLineAdd ClassRef ListID' => 'IDTYPE',
  'InvoiceLineAdd ClassRef FullName' => 'STRTYPE',
  'InvoiceLineAdd Amount' => 'AMTTYPE',
  'InvoiceLineAdd ServiceDate' => 'DATETYPE',
  'InvoiceLineAdd SalesTaxCodeRef ListID' => 'IDTYPE',
  'InvoiceLineAdd SalesTaxCodeRef FullName' => 'STRTYPE',
  'InvoiceLineAdd IsTaxable' => 'BOOLTYPE',
  'InvoiceLineAdd OverrideItemAccountRef ListID' => 'IDTYPE',
  'InvoiceLineAdd OverrideItemAccountRef FullName' => 'STRTYPE',
  'InvoiceLineAdd Other1' => 'STRTYPE',
  'InvoiceLineAdd Other2' => 'STRTYPE',
  'InvoiceLineAdd LinkToTxn TxnID' => 'IDTYPE',
  'InvoiceLineAdd LinkToTxn TxnLineID' => 'IDTYPE',
  'InvoiceLineAdd DataExt OwnerID' => 'GUIDTYPE',
  'InvoiceLineAdd DataExt DataExtName' => 'STRTYPE',
  'InvoiceLineAdd DataExt DataExtValue' => 'STRTYPE',
  'InvoiceLineGroupAdd ItemGroupRef ListID' => 'IDTYPE',
  'InvoiceLineGroupAdd ItemGroupRef FullName' => 'STRTYPE',
  'InvoiceLineGroupAdd Desc' => 'STRTYPE',
  'InvoiceLineGroupAdd Quantity' => 'QUANTYPE',
  'InvoiceLineGroupAdd UnitOfMeasure' => 'STRTYPE',
  'InvoiceLineGroupAdd ServiceDate' => 'DATETYPE',
  'InvoiceLineGroupAdd DataExt OwnerID' => 'GUIDTYPE',
  'InvoiceLineGroupAdd DataExt DataExtName' => 'STRTYPE',
  'InvoiceLineGroupAdd DataExt DataExtValue' => 'STRTYPE',
  'DiscountLineAdd Amount' => 'AMTTYPE',
  'DiscountLineAdd RatePercent' => 'PERCENTTYPE',
  'DiscountLineAdd IsTaxable' => 'BOOLTYPE',
  'DiscountLineAdd AccountRef ListID' => 'IDTYPE',
  'DiscountLineAdd AccountRef FullName' => 'STRTYPE',
  'SalesTaxLineAdd Amount' => 'AMTTYPE',
  'SalesTaxLineAdd RatePercent' => 'PERCENTTYPE',
  'SalesTaxLineAdd AccountRef ListID' => 'IDTYPE',
  'SalesTaxLineAdd AccountRef FullName' => 'STRTYPE',
  'ShippingLineAdd Amount' => 'AMTTYPE',
  'ShippingLineAdd AccountRef ListID' => 'IDTYPE',
  'ShippingLineAdd AccountRef FullName' => 'STRTYPE',
  'IncludeRetElement' => 'STRTYPE',
);
		
		return $paths;
	}
	
	protected function &_maxLengthPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 0,
  'CustomerRef FullName' => 209,
  'ClassRef ListID' => 0,
  'ClassRef FullName' => 209,
  'ARAccountRef ListID' => 0,
  'ARAccountRef FullName' => 209,
  'TemplateRef ListID' => 0,
  'TemplateRef FullName' => 209,
  'TxnDate' => 0,
  'RefNumber' => 11,
  'BillAddress Addr1' => 41,
  'BillAddress Addr2' => 41,
  'BillAddress Addr3' => 41,
  'BillAddress Addr4' => 41,
  'BillAddress Addr5' => 41,
  'BillAddress City' => 31,
  'BillAddress State' => 21,
  'BillAddress PostalCode' => 13,
  'BillAddress Country' => 31,
  'BillAddress Note' => 41,
  'ShipAddress Addr1' => 41,
  'ShipAddress Addr2' => 41,
  'ShipAddress Addr3' => 41,
  'ShipAddress Addr4' => 41,
  'ShipAddress Addr5' => 41,
  'ShipAddress City' => 31,
  'ShipAddress State' => 21,
  'ShipAddress PostalCode' => 13,
  'ShipAddress Country' => 31,
  'ShipAddress Note' => 41,
  'IsPending' => 0,
  'PONumber' => 25,
  'TermsRef ListID' => 0,
  'TermsRef FullName' => 209,
  'DueDate' => 0,
  'SalesRepRef ListID' => 0,
  'SalesRepRef FullName' => 209,
  'FOB' => 13,
  'ShipDate' => 0,
  'ShipMethodRef ListID' => 0,
  'ShipMethodRef FullName' => 209,
  'ItemSalesTaxRef ListID' => 0,
  'ItemSalesTaxRef FullName' => 209,
  'Memo' => 4095,
  'CustomerMsgRef ListID' => 0,
  'CustomerMsgRef FullName' => 209,
  'IsToBePrinted' => 0,
  'IsToBeEmailed' => 0,
  'IsTaxIncluded' => 0,
  'CustomerSalesTaxCodeRef ListID' => 0,
  'CustomerSalesTaxCodeRef FullName' => 209,
  'Other' => 29,
  'LinkToTxnID' => 0,
  'InvoiceLineAdd ItemRef ListID' => 0,
  'InvoiceLineAdd ItemRef FullName' => 209,
  'InvoiceLineAdd Desc' => 4095,
  'InvoiceLineAdd Quantity' => 0,
  'InvoiceLineAdd UnitOfMeasure' => 31,
  'InvoiceLineAdd Rate' => 0,
  'InvoiceLineAdd RatePercent' => 0,
  'InvoiceLineAdd PriceLevelRef ListID' => 0,
  'InvoiceLineAdd PriceLevelRef FullName' => 209,
  'InvoiceLineAdd ClassRef ListID' => 0,
  'InvoiceLineAdd ClassRef FullName' => 209,
  'InvoiceLineAdd Amount' => 0,
  'InvoiceLineAdd ServiceDate' => 0,
  'InvoiceLineAdd SalesTaxCodeRef ListID' => 0,
  'InvoiceLineAdd SalesTaxCodeRef FullName' => 209,
  'InvoiceLineAdd IsTaxable' => 0,
  'InvoiceLineAdd OverrideItemAccountRef ListID' => 0,
  'InvoiceLineAdd OverrideItemAccountRef FullName' => 209,
  'InvoiceLineAdd Other1' => 29,
  'InvoiceLineAdd Other2' => 29,
  'InvoiceLineAdd LinkToTxn TxnID' => 0,
  'InvoiceLineAdd LinkToTxn TxnLineID' => 0,
  'InvoiceLineAdd DataExt OwnerID' => 0,
  'InvoiceLineAdd DataExt DataExtName' => 31,
  'InvoiceLineAdd DataExt DataExtValue' => 0,
  'InvoiceLineGroupAdd ItemGroupRef ListID' => 0,
  'InvoiceLineGroupAdd ItemGroupRef FullName' => 209,
  'InvoiceLineGroupAdd Desc' => 4095,
  'InvoiceLineGroupAdd Quantity' => 0,
  'InvoiceLineGroupAdd UnitOfMeasure' => 31,
  'InvoiceLineGroupAdd ServiceDate' => 0,
  'InvoiceLineGroupAdd DataExt OwnerID' => 0,
  'InvoiceLineGroupAdd DataExt DataExtName' => 31,
  'InvoiceLineGroupAdd DataExt DataExtValue' => 0,
  'DiscountLineAdd Amount' => 0,
  'DiscountLineAdd RatePercent' => 0,
  'DiscountLineAdd IsTaxable' => 0,
  'DiscountLineAdd AccountRef ListID' => 0,
  'DiscountLineAdd AccountRef FullName' => 209,
  'SalesTaxLineAdd Amount' => 0,
  'SalesTaxLineAdd RatePercent' => 0,
  'SalesTaxLineAdd AccountRef ListID' => 0,
  'SalesTaxLineAdd AccountRef FullName' => 209,
  'ShippingLineAdd Amount' => 0,
  'ShippingLineAdd AccountRef ListID' => 0,
  'ShippingLineAdd AccountRef FullName' => 209,
  'IncludeRetElement' => 50,
);
		
		return $paths;
	}
	
	protected function &_isOptionalPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => true,
  'CustomerRef FullName' => true,
  'ClassRef ListID' => true,
  'ClassRef FullName' => true,
  'ARAccountRef ListID' => true,
  'ARAccountRef FullName' => true,
  'TemplateRef ListID' => true,
  'TemplateRef FullName' => true,
  'TxnDate' => true,
  'RefNumber' => true,
  'BillAddress Addr1' => true,
  'BillAddress Addr2' => true,
  'BillAddress Addr3' => true,
  'BillAddress Addr4' => true,
  'BillAddress Addr5' => true,
  'BillAddress City' => true,
  'BillAddress State' => true,
  'BillAddress PostalCode' => true,
  'BillAddress Country' => true,
  'BillAddress Note' => true,
  'ShipAddress Addr1' => true,
  'ShipAddress Addr2' => true,
  'ShipAddress Addr3' => true,
  'ShipAddress Addr4' => true,
  'ShipAddress Addr5' => true,
  'ShipAddress City' => true,
  'ShipAddress State' => true,
  'ShipAddress PostalCode' => true,
  'ShipAddress Country' => true,
  'ShipAddress Note' => true,
  'IsPending' => true,
  'PONumber' => true,
  'TermsRef ListID' => true,
  'TermsRef FullName' => true,
  'DueDate' => true,
  'SalesRepRef ListID' => true,
  'SalesRepRef FullName' => true,
  'FOB' => true,
  'ShipDate' => true,
  'ShipMethodRef ListID' => true,
  'ShipMethodRef FullName' => true,
  'ItemSalesTaxRef ListID' => true,
  'ItemSalesTaxRef FullName' => true,
  'Memo' => true,
  'CustomerMsgRef ListID' => true,
  'CustomerMsgRef FullName' => true,
  'IsToBePrinted' => true,
  'IsToBeEmailed' => true,
  'IsTaxIncluded' => true,
  'CustomerSalesTaxCodeRef ListID' => true,
  'CustomerSalesTaxCodeRef FullName' => true,
  'Other' => true,
  'LinkToTxnID' => true,
  'InvoiceLineAdd ItemRef ListID' => true,
  'InvoiceLineAdd ItemRef FullName' => true,
  'InvoiceLineAdd Desc' => true,
  'InvoiceLineAdd Quantity' => true,
  'InvoiceLineAdd UnitOfMeasure' => true,
  'InvoiceLineAdd Rate' => false,
  'InvoiceLineAdd RatePercent' => false,
  'InvoiceLineAdd PriceLevelRef ListID' => true,
  'InvoiceLineAdd PriceLevelRef FullName' => true,
  'InvoiceLineAdd ClassRef ListID' => true,
  'InvoiceLineAdd ClassRef FullName' => true,
  'InvoiceLineAdd Amount' => true,
  'InvoiceLineAdd ServiceDate' => true,
  'InvoiceLineAdd SalesTaxCodeRef ListID' => true,
  'InvoiceLineAdd SalesTaxCodeRef FullName' => true,
  'InvoiceLineAdd IsTaxable' => true,
  'InvoiceLineAdd OverrideItemAccountRef ListID' => true,
  'InvoiceLineAdd OverrideItemAccountRef FullName' => true,
  'InvoiceLineAdd Other1' => true,
  'InvoiceLineAdd Other2' => true,
  'InvoiceLineAdd LinkToTxn TxnID' => false,
  'InvoiceLineAdd LinkToTxn TxnLineID' => false,
  'InvoiceLineAdd DataExt OwnerID' => false,
  'InvoiceLineAdd DataExt DataExtName' => false,
  'InvoiceLineAdd DataExt DataExtValue' => false,
  'InvoiceLineGroupAdd ItemGroupRef ListID' => true,
  'InvoiceLineGroupAdd ItemGroupRef FullName' => true,
  'InvoiceLineGroupAdd Desc' => true,
  'InvoiceLineGroupAdd Quantity' => true,
  'InvoiceLineGroupAdd UnitOfMeasure' => true,
  'InvoiceLineGroupAdd ServiceDate' => true,
  'InvoiceLineGroupAdd DataExt OwnerID' => false,
  'InvoiceLineGroupAdd DataExt DataExtName' => false,
  'InvoiceLineGroupAdd DataExt DataExtValue' => false,
  'DiscountLineAdd Amount' => true,
  'DiscountLineAdd RatePercent' => false,
  'DiscountLineAdd IsTaxable' => true,
  'DiscountLineAdd AccountRef ListID' => true,
  'DiscountLineAdd AccountRef FullName' => true,
  'SalesTaxLineAdd Amount' => true,
  'SalesTaxLineAdd RatePercent' => false,
  'SalesTaxLineAdd AccountRef ListID' => true,
  'SalesTaxLineAdd AccountRef FullName' => true,
  'ShippingLineAdd Amount' => true,
  'ShippingLineAdd AccountRef ListID' => true,
  'ShippingLineAdd AccountRef FullName' => true,
  'IncludeRetElement' => true,
);
	}
	
	protected function &_sinceVersionPaths()
	{
		static $paths = array (
  'CustomerRef ListID' => 999.99,
  'CustomerRef FullName' => 999.99,
  'ClassRef ListID' => 999.99,
  'ClassRef FullName' => 999.99,
  'ARAccountRef ListID' => 999.99,
  'ARAccountRef FullName' => 999.99,
  'TemplateRef ListID' => 999.99,
  'TemplateRef FullName' => 999.99,
  'TxnDate' => 999.99,
  'RefNumber' => 999.99,
  'BillAddress Addr1' => 999.99,
  'BillAddress Addr2' => 999.99,
  'BillAddress Addr3' => 999.99,
  'BillAddress Addr4' => 2,
  'BillAddress Addr5' => 6,
  'BillAddress City' => 999.99,
  'BillAddress State' => 999.99,
  'BillAddress PostalCode' => 999.99,
  'BillAddress Country' => 999.99,
  'BillAddress Note' => 6,
  'ShipAddress Addr1' => 999.99,
  'ShipAddress Addr2' => 999.99,
  'ShipAddress Addr3' => 999.99,
  'ShipAddress Addr4' => 2,
  'ShipAddress Addr5' => 6,
  'ShipAddress City' => 999.99,
  'ShipAddress State' => 999.99,
  'ShipAddress PostalCode' => 999.99,
  'ShipAddress Country' => 999.99,
  'ShipAddress Note' => 6,
  'IsPending' => 999.99,
  'PONumber' => 999.99,
  'TermsRef ListID' => 999.99,
  'TermsRef FullName' => 999.99,
  'DueDate' => 999.99,
  'SalesRepRef ListID' => 999.99,
  'SalesRepRef FullName' => 999.99,
  'FOB' => 999.99,
  'ShipDate' => 999.99,
  'ShipMethodRef ListID' => 999.99,
  'ShipMethodRef FullName' => 999.99,
  'ItemSalesTaxRef ListID' => 999.99,
  'ItemSalesTaxRef FullName' => 999.99,
  'Memo' => 999.99,
  'CustomerMsgRef ListID' => 999.99,
  'CustomerMsgRef FullName' => 999.99,
  'IsToBePrinted' => 999.99,
  'IsToBeEmailed' => 6,
  'IsTaxIncluded' => 6,
  'CustomerSalesTaxCodeRef ListID' => 999.99,
  'CustomerSalesTaxCodeRef FullName' => 999.99,
  'Other' => 6,
  'LinkToTxnID' => 6,
  'InvoiceLineAdd ItemRef ListID' => 999.99,
  'InvoiceLineAdd ItemRef FullName' => 999.99,
  'InvoiceLineAdd Desc' => 999.99,
  'InvoiceLineAdd Quantity' => 999.99,
  'InvoiceLineAdd UnitOfMeasure' => 7,
  'InvoiceLineAdd Rate' => 999.99,
  'InvoiceLineAdd RatePercent' => 999.99,
  'InvoiceLineAdd PriceLevelRef ListID' => 999.99,
  'InvoiceLineAdd PriceLevelRef FullName' => 999.99,
  'InvoiceLineAdd ClassRef ListID' => 999.99,
  'InvoiceLineAdd ClassRef FullName' => 999.99,
  'InvoiceLineAdd Amount' => 999.99,
  'InvoiceLineAdd ServiceDate' => 999.99,
  'InvoiceLineAdd SalesTaxCodeRef ListID' => 999.99,
  'InvoiceLineAdd SalesTaxCodeRef FullName' => 999.99,
  'InvoiceLineAdd IsTaxable' => 4,
  'InvoiceLineAdd OverrideItemAccountRef ListID' => 999.99,
  'InvoiceLineAdd OverrideItemAccountRef FullName' => 999.99,
  'InvoiceLineAdd Other1' => 6,
  'InvoiceLineAdd Other2' => 6,
  'InvoiceLineAdd LinkToTxn TxnID' => 999.99,
  'InvoiceLineAdd LinkToTxn TxnLineID' => 999.99,
  'InvoiceLineAdd DataExt OwnerID' => 999.99,
  'InvoiceLineAdd DataExt DataExtName' => 999.99,
  'InvoiceLineAdd DataExt DataExtValue' => 999.99,
  'InvoiceLineGroupAdd ItemGroupRef ListID' => 999.99,
  'InvoiceLineGroupAdd ItemGroupRef FullName' => 999.99,
  'InvoiceLineGroupAdd Desc' => 999.99,
  'InvoiceLineGroupAdd Quantity' => 999.99,
  'InvoiceLineGroupAdd UnitOfMeasure' => 7,
  'InvoiceLineGroupAdd ServiceDate' => 999.99,
  'InvoiceLineGroupAdd DataExt OwnerID' => 999.99,
  'InvoiceLineGroupAdd DataExt DataExtName' => 999.99,
  'InvoiceLineGroupAdd DataExt DataExtValue' => 999.99,
  'DiscountLineAdd Amount' => 999.99,
  'DiscountLineAdd RatePercent' => 999.99,
  'DiscountLineAdd IsTaxable' => 4,
  'DiscountLineAdd AccountRef ListID' => 999.99,
  'DiscountLineAdd AccountRef FullName' => 999.99,
  'SalesTaxLineAdd Amount' => 999.99,
  'SalesTaxLineAdd RatePercent' => 999.99,
  'SalesTaxLineAdd AccountRef ListID' => 999.99,
  'SalesTaxLineAdd AccountRef FullName' => 999.99,
  'ShippingLineAdd Amount' => 999.99,
  'ShippingLineAdd AccountRef ListID' => 999.99,
  'ShippingLineAdd AccountRef FullName' => 999.99,
  'IncludeRetElement' => 4,
);
		
		return $paths;
	}
	
	protected function &_isRepeatablePaths()
	{
		static $paths = array (
  'CustomerRef ListID' => false,
  'CustomerRef FullName' => false,
  'ClassRef ListID' => false,
  'ClassRef FullName' => false,
  'ARAccountRef ListID' => false,
  'ARAccountRef FullName' => false,
  'TemplateRef ListID' => false,
  'TemplateRef FullName' => false,
  'TxnDate' => false,
  'RefNumber' => false,
  'BillAddress Addr1' => false,
  'BillAddress Addr2' => false,
  'BillAddress Addr3' => false,
  'BillAddress Addr4' => false,
  'BillAddress Addr5' => false,
  'BillAddress City' => false,
  'BillAddress State' => false,
  'BillAddress PostalCode' => false,
  'BillAddress Country' => false,
  'BillAddress Note' => false,
  'ShipAddress Addr1' => false,
  'ShipAddress Addr2' => false,
  'ShipAddress Addr3' => false,
  'ShipAddress Addr4' => false,
  'ShipAddress Addr5' => false,
  'ShipAddress City' => false,
  'ShipAddress State' => false,
  'ShipAddress PostalCode' => false,
  'ShipAddress Country' => false,
  'ShipAddress Note' => false,
  'IsPending' => false,
  'PONumber' => false,
  'TermsRef ListID' => false,
  'TermsRef FullName' => false,
  'DueDate' => false,
  'SalesRepRef ListID' => false,
  'SalesRepRef FullName' => false,
  'FOB' => false,
  'ShipDate' => false,
  'ShipMethodRef ListID' => false,
  'ShipMethodRef FullName' => false,
  'ItemSalesTaxRef ListID' => false,
  'ItemSalesTaxRef FullName' => false,
  'Memo' => false,
  'CustomerMsgRef ListID' => false,
  'CustomerMsgRef FullName' => false,
  'IsToBePrinted' => false,
  'IsToBeEmailed' => false,
  'IsTaxIncluded' => false,
  'CustomerSalesTaxCodeRef ListID' => false,
  'CustomerSalesTaxCodeRef FullName' => false,
  'Other' => false,
  'LinkToTxnID' => true,
  'InvoiceLineAdd ItemRef ListID' => false,
  'InvoiceLineAdd ItemRef FullName' => false,
  'InvoiceLineAdd Desc' => false,
  'InvoiceLineAdd Quantity' => false,
  'InvoiceLineAdd UnitOfMeasure' => false,
  'InvoiceLineAdd Rate' => false,
  'InvoiceLineAdd RatePercent' => false,
  'InvoiceLineAdd PriceLevelRef ListID' => false,
  'InvoiceLineAdd PriceLevelRef FullName' => false,
  'InvoiceLineAdd ClassRef ListID' => false,
  'InvoiceLineAdd ClassRef FullName' => false,
  'InvoiceLineAdd Amount' => false,
  'InvoiceLineAdd ServiceDate' => false,
  'InvoiceLineAdd SalesTaxCodeRef ListID' => false,
  'InvoiceLineAdd SalesTaxCodeRef FullName' => false,
  'InvoiceLineAdd IsTaxable' => false,
  'InvoiceLineAdd OverrideItemAccountRef ListID' => false,
  'InvoiceLineAdd OverrideItemAccountRef FullName' => false,
  'InvoiceLineAdd Other1' => false,
  'InvoiceLineAdd Other2' => false,
  'InvoiceLineAdd LinkToTxn TxnID' => false,
  'InvoiceLineAdd LinkToTxn TxnLineID' => false,
  'InvoiceLineAdd DataExt OwnerID' => false,
  'InvoiceLineAdd DataExt DataExtName' => false,
  'InvoiceLineAdd DataExt DataExtValue' => false,
  'InvoiceLineGroupAdd ItemGroupRef ListID' => false,
  'InvoiceLineGroupAdd ItemGroupRef FullName' => false,
  'InvoiceLineGroupAdd Desc' => false,
  'InvoiceLineGroupAdd Quantity' => false,
  'InvoiceLineGroupAdd UnitOfMeasure' => false,
  'InvoiceLineGroupAdd ServiceDate' => false,
  'InvoiceLineGroupAdd DataExt OwnerID' => false,
  'InvoiceLineGroupAdd DataExt DataExtName' => false,
  'InvoiceLineGroupAdd DataExt DataExtValue' => false,
  'DiscountLineAdd Amount' => false,
  'DiscountLineAdd RatePercent' => false,
  'DiscountLineAdd IsTaxable' => false,
  'DiscountLineAdd AccountRef ListID' => false,
  'DiscountLineAdd AccountRef FullName' => false,
  'SalesTaxLineAdd Amount' => false,
  'SalesTaxLineAdd RatePercent' => false,
  'SalesTaxLineAdd AccountRef ListID' => false,
  'SalesTaxLineAdd AccountRef FullName' => false,
  'ShippingLineAdd Amount' => false,
  'ShippingLineAdd AccountRef ListID' => false,
  'ShippingLineAdd AccountRef FullName' => false,
  'IncludeRetElement' => true,
);
			
		return $paths;
	}
	
	/*
	abstract protected function &_inLocalePaths()
	{
		static $paths = array(
			'FirstName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ), 
			'LastName' => array( 'QBD', 'QBCA', 'QBUK', 'QBAU' ),
			);
		
		return $paths;
	}
	*/
	
	protected function &_reorderPathsPaths()
	{
		static $paths = array (
  0 => 'CustomerRef ListID',
  1 => 'CustomerRef FullName',
  2 => 'ClassRef ListID',
  3 => 'ClassRef FullName',
  4 => 'ARAccountRef ListID',
  5 => 'ARAccountRef FullName',
  6 => 'TemplateRef ListID',
  7 => 'TemplateRef FullName',
  8 => 'TxnDate',
  9 => 'RefNumber',
  10 => 'BillAddress Addr1',
  11 => 'BillAddress Addr2',
  12 => 'BillAddress Addr3',
  13 => 'BillAddress Addr4',
  14 => 'BillAddress Addr5',
  15 => 'BillAddress City',
  16 => 'BillAddress State',
  17 => 'BillAddress PostalCode',
  18 => 'BillAddress Country',
  19 => 'BillAddress Note',
  20 => 'ShipAddress Addr1',
  21 => 'ShipAddress Addr2',
  22 => 'ShipAddress Addr3',
  23 => 'ShipAddress Addr4',
  24 => 'ShipAddress Addr5',
  25 => 'ShipAddress City',
  26 => 'ShipAddress State',
  27 => 'ShipAddress PostalCode',
  28 => 'ShipAddress Country',
  29 => 'ShipAddress Note',
  30 => 'IsPending',
  31 => 'PONumber',
  32 => 'TermsRef ListID',
  33 => 'TermsRef FullName',
  34 => 'DueDate',
  35 => 'SalesRepRef ListID',
  36 => 'SalesRepRef FullName',
  37 => 'FOB',
  38 => 'ShipDate',
  39 => 'ShipMethodRef ListID',
  40 => 'ShipMethodRef FullName',
  41 => 'ItemSalesTaxRef ListID',
  42 => 'ItemSalesTaxRef FullName',
  43 => 'Memo',
  44 => 'CustomerMsgRef ListID',
  45 => 'CustomerMsgRef FullName',
  46 => 'IsToBePrinted',
  47 => 'IsToBeEmailed',
  48 => 'IsTaxIncluded',
  49 => 'CustomerSalesTaxCodeRef ListID',
  50 => 'CustomerSalesTaxCodeRef FullName',
  51 => 'Other',
  52 => 'LinkToTxnID',
  53 => 'InvoiceLineAdd',
  54 => 'InvoiceLineAdd ItemRef',
  55 => 'InvoiceLineAdd ItemRef ListID',
  56 => 'InvoiceLineAdd ItemRef FullName',
  57 => 'InvoiceLineAdd Desc',
  58 => 'InvoiceLineAdd Quantity',
  59 => 'InvoiceLineAdd UnitOfMeasure',
  60 => 'InvoiceLineAdd Rate',
  61 => 'InvoiceLineAdd RatePercent',
  62 => 'InvoiceLineAdd PriceLevelRef ListID',
  63 => 'InvoiceLineAdd PriceLevelRef FullName',
  64 => 'InvoiceLineAdd ClassRef ListID',
  65 => 'InvoiceLineAdd ClassRef FullName',
  66 => 'InvoiceLineAdd Amount',
  67 => 'InvoiceLineAdd ServiceDate',
  68 => 'InvoiceLineAdd SalesTaxCodeRef ListID',
  69 => 'InvoiceLineAdd SalesTaxCodeRef FullName',
  70 => 'InvoiceLineAdd IsTaxable',
  71 => 'InvoiceLineAdd OverrideItemAccountRef ListID',
  72 => 'InvoiceLineAdd OverrideItemAccountRef FullName',
  73 => 'InvoiceLineAdd Other1',
  74 => 'InvoiceLineAdd Other2',
  75 => 'InvoiceLineAdd LinkToTxn TxnID',
  76 => 'InvoiceLineAdd LinkToTxn TxnLineID',
  77 => 'InvoiceLineAdd DataExt OwnerID',
  78 => 'InvoiceLineAdd DataExt DataExtName',
  79 => 'InvoiceLineAdd DataExt DataExtValue',
  80 => 'InvoiceLineGroupAdd ItemGroupRef ListID',
  81 => 'InvoiceLineGroupAdd ItemGroupRef FullName',
  82 => 'InvoiceLineGroupAdd Desc',
  83 => 'InvoiceLineGroupAdd Quantity',
  84 => 'InvoiceLineGroupAdd UnitOfMeasure',
  85 => 'InvoiceLineGroupAdd ServiceDate',
  86 => 'InvoiceLineGroupAdd DataExt OwnerID',
  87 => 'InvoiceLineGroupAdd DataExt DataExtName',
  88 => 'InvoiceLineGroupAdd DataExt DataExtValue',
  89 => 'DiscountLineAdd Amount',
  90 => 'DiscountLineAdd RatePercent',
  91 => 'DiscountLineAdd IsTaxable',
  92 => 'DiscountLineAdd AccountRef ListID',
  93 => 'DiscountLineAdd AccountRef FullName',
  94 => 'SalesTaxLineAdd Amount',
  95 => 'SalesTaxLineAdd RatePercent',
  96 => 'SalesTaxLineAdd AccountRef ListID',
  97 => 'SalesTaxLineAdd AccountRef FullName',
  98 => 'ShippingLineAdd Amount',
  99 => 'ShippingLineAdd AccountRef ListID',
  100 => 'ShippingLineAdd AccountRef FullName',
  101 => 'IncludeRetElement',
);
			
		return $paths;
	}
}

?>