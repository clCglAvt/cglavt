 BEGIN
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_COMPANY_SENTBYMAIL' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_COMPANY_CREATE' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_PROPAL_VALIDATE' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_PROPAL_SENTBYMAIL' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_VALIDATE' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_SENTBYMAIL' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_BILL_VALIDATE' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_BILL_PAYED' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_BILL_CANCEL' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_BILL_SENTBYMAIL' AND entity = 1;
DELETE FROM llx_const WHERE name = 'MAIN_AGENDA_ACTIONAUTO_BILL_UNVALIDATE' AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_APPROVE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_REFUSE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_SENTBYMAIL') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_PAYED') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_SENTBYMAIL') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_CONTRACT_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_FICHINTER_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_SHIPPING_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_SHIPPING_SENTBYMAIL') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_MEMBER_VALIDATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_MEMBER_SUBSCRIPTION') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_MEMBER_RESILIATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_MEMBER_DELETE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_FICHINTER_SENTBYMAIL') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_PROJECT_CREATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_PROPAL_CLOSE_SIGNED') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_PROPAL_CLOSE_REFUSED') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_CANCELED') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_MEMBER_MODIFY') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_TASK_CREATE') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_TASK_MODIFY') AND entity = 1;
DELETE FROM llx_const WHERE (name = 'MAIN_AGENDA_ACTIONAUTO_TASK_DELETE') AND entity = 1;
COMMIT