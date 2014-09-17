<?php
/**
 * @class  indecoxbusAdminView
 * @author CRA:pinetree (vanadate.kr@gmail.com)
 * @brief  indecoxbus module's admin view class
 **/

class indecoxbusAdminView extends indecoxbus {
	 
	/**
	 * @brief initialization
	 **/
	function init() {
		// 탬플릿 경로 설정
		$template_path = sprintf("%stpl/",$this->module_path);
		$this->setTemplatePath($template_path);
	}
	
	/**
	 * @function dispIndecoxbusAdminList
	 * @brief 버스들의 목록들을 보여준다.
	 */
	function dispIndecoxbusAdminList() {
		// 탬플릿 파일 설정
		$this->setTemplateFile('list');
		
		// TODO 모든 버스 목록들을 불러와서 출력(navigation까지는 필요 없을듯.)
		$bus_list = executeQueryArray('indecoxbus.getBusList');
		Context::set('bus_list', $bus_list->data);
	}
	
	/**
	 * @function dispIndecoxAdminError
	 * @brief 에러코드를 나타낼 함수와 페이지
	 * @param error_code : 에러 코드
	 * @param error_description : 에러 설명
	 */
	function dispIndecoxAdminError() {
		$error_code = Context::get('error_code');
		$error_description = Context::get('error_description');
		Context::set('error_code', $error_code);
		Context::set('error_description', $error_escription);
		$this->setTemplateFile('error');
	}
	
	/**
	 * @function dispIndecoxbusAdminInsert
	 * @brief 버스를 새로 추가시킨다.
	 */
	function dispIndecoxbusAdminInsert() {
		// 필요 변수 던져주기
		// 모듈 경로
		$module_path = ModuleHandler::getModulePath('indecoxbus');
		$member_col = array('member_srl', 'user_id', 'user_name', 'nick_name');
		
		// bus_srl이 들어왔을 경우 수정으로 알고 해당하는 버스의 정보를 던져준다.
		if (Context::get('bus_srl')) {
			$args->bus_srl = Context::get('bus_srl');
			$output = executeQuery('indecoxbus.getBusByBusSrl', $args);
			// 관련된 member_srl의 사용자 정보도 던지기
			$oMemberModel = &getModel('member');
			$member_info = $oMemberModel->getMemberInfoByMemberSrl($output->data->member_srl, 0, $member_col);
			Context::set('bus_info', $output->data);
			Context::set('member_info', $member_info);
		}
		
		// 템플릿 파일 설정
		$this->setTemplateFile('insert');
	}
}
?>
