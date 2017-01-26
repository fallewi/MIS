<?php

/**
 * Copyright (c) 2015 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights
 * Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distributed under a limited and restricted
 * license – please visit www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE. TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, IN NO
 * EVENT WILL SLI BE LIABLE TO YOU OR ANY OTHER PARTY FOR ANY GENERAL, DIRECT,
 * INDIRECT, SPECIAL, INCIDENTAL OR CONSEQUENTIAL LOSS OR DAMAGES OF ANY
 * CHARACTER ARISING OUT OF THE USE OF THE CODE AND/OR THE LICENSE INCLUDING
 * BUT NOT LIMITED TO PERSONAL INJURY, LOSS OF DATA, LOSS OF PROFITS, LOSS OF
 * ASSIGNMENTS, DATA OR OUTPUT FROM THE SERVICE BEING RENDERED INACCURATE,
 * FAILURE OF CODE, SERVER DOWN TIME, DAMAGES FOR LOSS OF GOODWILL, BUSINESS
 * INTERRUPTION, COMPUTER FAILURE OR MALFUNCTION, OR ANY AND ALL OTHER DAMAGES
 * OR LOSSES OF WHATEVER NATURE, EVEN IF SLI HAS BEEN INFORMED OF THE
 * POSSIBILITY OF SUCH DAMAGES.
 */

/**
 * Generates feed demo data.
 *
 * @package    SLI
 * @subpackage Search
 */
class SLI_Search_Model_Generators_DemoGenerator implements SLI_Search_Model_Generators_GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateForStoreId($storeId, SLI_Search_Model_Generators_GeneratorContext $generatorContext)
    {
        $logger = $generatorContext->getLogger();
        $xmlWriter = $generatorContext->getXmlWriter();

        $logger->debug(sprintf('[%s] Starting product demo XML generation', $storeId));

        $xmlWriter->startElement('products');

        $demoProduct = <<<EOS

  <product id="558" sku="hbm005">
   <entity_id>558</entity_id>
   <entity_type_id>4</entity_type_id>
   <attribute_set_id>10</attribute_set_id>
   <type_id>downloadable</type_id>
   <sku>hbm005</sku>
   <has_options>1</has_options>
   <required_options>0</required_options>
   <created_at>2013-03-25 19:06:32</created_at>
   <updated_at>2013-05-14 18:49:42</updated_at>
   <status>1</status>
   <price>2.0000</price>
   <tax_class_id>0</tax_class_id>
   <final_price>2.0000</final_price>
   <minimal_price>2.0000</minimal_price>
   <min_price>2.0000</min_price>
   <max_price>2.0000</max_price>
   <tier_price/>
   <inventory_in_stock>1</inventory_in_stock>
   <name>Falling by I Am Not Lefthanded</name>
   <url_path>falling-by-i-am-not-lefthanded.html</url_path>
   <url_key>falling-by-i-am-not-lefthanded</url_key>
   <visibility>4</visibility>
   <is_salable>1</is_salable>
   <is_in_stock>1</is_in_stock>
   <category_ids>
    <value_1>22</value_1>
   </category_ids>
   <related_products/>
   <related_product_ids/>
  </product>

EOS;

        $xmlWriter->writeRaw($demoProduct);

        $xmlWriter->endElement();

        $logger->debug(sprintf('[%s] Finished writing product demo', $storeId));

        return true;
    }
}
