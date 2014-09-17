/**
 * 삽입, 또는 수정 페이지에 관한 자바스크립트 라이브러리
 */
jQuery(function($){
	
	/**
	 * 클로져 변수들...
	 */
	var Insert = {};
	
	// 드롭다운 박스가 떴을 때 몇 번째 드롭다운 박스가 선택되었는지 표기한다.
	// -1일 경우 아무것도 선택되지 않은 경우이다.
	var selected_member_info = -1;
	
	/**
	 *
	 */
	$(document).ready(function() {
		
	});
	
	/**
	 * 폼의 승인 버튼을 눌렀을 때
	 * exec_xml() 을 사용하여 폼 정보 전송, 결과 출력.
	 */
	$('form').submit(function() {
		// 필요한 정보 받기
		var $my_form = $(this);
		
		var module = $my_form.find('input[name="module"]').val();
		var act = $my_form.find('input[name="act"]').val();
		
		// 던질 값들 결정
		var params = {
				name: $my_form.find('input[name="name"]').val(),
				max_volume: $my_form.find('input[name="max_volume"]').val(),
				bus_srl: $my_form.find('input[name="bus_srl"]').val(),
				member_srl: $my_form.find('input[name="member_srl"]').val()
		};
		// 받을 값들 결정
		var responses = ['error', 'message'];
		
		// 값 보내기
		exec_xml(module, act, params, callback_submit, responses, params, $my_form);
		
		return false;
	});
	
	/**
	 * submit의 콜백 함수
	 */
	function callback_submit(ret_obj, responses, params, form) {
		// 문제가 없을 경우 dispIndecoxbusAdminList로 넘어간다.
		if (parseInt(ret_obj['error']) == 0) {
			window.location = form.find('input[name="redirect_url"]').val();
		}
		// 문제가 있을 경우 문제를 출력해줌.
		else {
			alert(parseInt(ret_obj['error']) + ' : ' + ret_obj['message']);
		}
	}
	
	/**
	 * 버스 이름은 중복되는 값이 있으면 안된다.
	 * 해당 텍스트 박스가 포커스를 잃는 순간 서버에 요청을 해서 중복되는 버스 이름이 있는지 확인한다.
	 */
	$('input[name="name"]').focusout(function() {
		var responses = ['error', 'message'];
		var params = {
				name: $('input[name="name"]').val()
			};
		
		params.name = $.trim(params.name);
		
		if (params.name == "") {
			return false;
		}
		
		exec_xml('indecoxbus', 'getIndecoxbusAdminCheckBusNameOverlap', params, callback_focusout, responses, params);
		
		return false;
	});
	
	/**
	 * 버스의 중복값이 있는지 없는지 확인하고 콜백 함수
	 */
	function callback_focusout(ret_obj, responses, params) {
		var error = parseInt(ret_obj['error']);
		var message = ret_obj['message'];
		var $inputName = $('input[name="name"]');
		// message = success 는 중복된 값이 없다는 것
		if (message == 'success') {
			$inputName.siblings('.description').text('');
			$inputName.parent().parent().find('.description').text('');
		}
		// message = overlaps 는 중복된 값이 있다는 것
		if (message == 'overlaps') {
			//$inputName.focus();
			$inputName.parent().parent().find('.description').text('중복되는 값이 있습니다.');
		}
		return false;
	}
	
	/*
	 * 엔터키가 눌러졌으면 선택된 항목의 정보를 연결ID 택스트 박스에 넣는다.
	 */ 
	$('#txt_member').keypress(function(event){
		if (event.keyCode == 13) {
			//selected_member_info
			var $myMemberInfo = $('.member_info:eq(' + selected_member_info + ')');
			var member_srl = $myMemberInfo.children('.member_srl').val();
			var user_name = $myMemberInfo.children('.user_name').text();
			var nick_name = $myMemberInfo.children('.nick_name').text();
			var user_id = $myMemberInfo.children('.user_id').text();
			
			$('#hidden_member_srl').val(member_srl);
			$('#txt_member').val(nick_name);
			
			$('.member_info').remove();
			
			return false;
		}
	});
	
	/**
	 * 연결 사용자에 키를 누르면 해당하는 사용자들(id와 이름 대조)를 찾아서 리스트를 출력시켜준다.
	 * 만약에 화살표 위와 화살표 아래 키가 눌러지면
	 * 드롭다운 리스트가 있는지 확인을 한 다음
	 * 있다면 선택바를 아래위로 움직이도록 한다.
	 */
	$('#txt_member').keyup(function(event) {
		var responses = [];
		var params = {};
		
		event.stopPropagation();
		
		// 만약 숫자나 알파벳이 눌러진다면 입력된 데이터를 서버에 리퀘스트 보낸다.
		var ek = event.keyCode;
		if (ek > 64 && ek < 91 || ek > 47 && ek < 57 || ek == 8){
			responses = ['error', 'message', 'user_info'];
			params = {
				key: $(this).val()
			};
			params.key = $.trim(params.key);
			if (params.key == "") {
				return false;
			}
			exec_xml('indecoxbus', 'getIndecoxbusAdminSearchNameId', params, callback_searchNameId, responses, params);
		}
		
		// 만약에 화살표키라면 드롭다운 리스트가 있는지 확인한 뒤 있다면 해당쪽으로 선택을 이동시킨다.
		// 드롭다운 리스트가 있는지 확인
		var length = $('.member_info').length;
		if (length == 0 || selected_member_info < 0)
			return false;
		
		// 아래 화살표(40)이 눌러졌다면
		if (event.keyCode == 40) {
			// 하나짜리일 때 무시, 마지막 항목이 이미 선택되었을 때 무시
			if (length == 1 || (selected_member_info == length - 1)) {
				return false;
			}
			// 선택 바를 아래로 내려준다.
			lighten_member_info($('.member_info:eq(' + selected_member_info + ')'));
			selected_member_info++;
			darken_member_info($('.member_info:eq(' + selected_member_info + ')'));
			return false;
		}
		
		// 위 화살표(38)이 눌러졌다면
		if (event.keyCode == 38) {
			// 하나짜리일 때 무시, 첫번째 항복이 이미 선택되었을 때 무시
			if (length == 1 || (selected_member_info == 0)) {
				return false;
			}
			// 선택 바를 위로 올려줌
			lighten_member_info($('.member_info:eq(' + selected_member_info + ')'));
			selected_member_info--;
			darken_member_info($('.member_info:eq(' + selected_member_info + ')'));
			return false;
		}
		return false;
	});
	
	/**
	 * 서버에서 사용자들을 찾아서 결과를 드롭다운 박스 형식으로 출력시킨다.
	 */
	function callback_searchNameId(ret_obj, responses, params) {
		
		if (ret_obj['user_info'] === null) {
			$('.member_info').remove();
			return false;
		}
		
		var user_info = ret_obj['user_info'].item;
		var type = typeof(user_info.length);
		var result = new Object();
		
		// 결과가 여러개 날라왔을 경우
		if (type.toLowerCase() == 'number') {
			result = user_info;
		}
		// 결과가 하나만 날라왔을 경우
		else {
			result = [user_info];
		}
		
		$('.member_info').remove();
		var $member_sample = $('#member_sample');
		
		// 결과 값 화면에 출력
		for (i in result) {
			var $member_info = $member_sample.clone();
			
			$member_info.addClass('member_info');
			$member_info.css('display', 'block');
			$member_info.attr('index', i);
			$member_info.removeAttr('id');
			$member_info.find('.user_id').text(result[i].user_id);
			$member_info.find('.user_name').text(result[i].user_name);
			$member_info.find('.nick_name').text(result[i].nick_name);
			$member_info.find('.member_srl').val(result[i].member_srl);
			
			// 이벤트 할당 - 클릭했을 때 해당 사용자의 정보가 txt_member와 member_srl(hidden)에 적용이 되어야 한다.
			$member_info.bind('click', apply_member_info);
			// 이벤트 할당 - 마우스를 올렸을 때 선택된 사항의 색깔은 찐하게 헤야하고 엔터키를 누르면 바로 적용이 가능하도록 만들기
			$member_info.bind('mouseenter', member_info_mouse_enter);
			// 이벤트 할당 - 마우스를 내렸을 때 선택된 사항의 색깔은 원래대로 되돌리고 엔터키를 누르면 바로 적용이 가능하도록 만들기
			$member_info.bind('mouseleave', member_info_mouse_leave);
			$('.dummy').before($member_info);
		}
		// 위치, 모양새  잡아주기
		var offset = $('#txt_member').offset();
		$('div .drop_down_list').css('position', 'absolute');
		$('div .drop_down_list').css('left', $('#txt_member').css('left'));
		$('div .drop_down_list').css('top', $('#txt_member').css('top'));
		
		// 첫 번째 요소를 선택한다.
		selected_member_info = 0;
		// 첫 번째 요소를 진하게 칠하기
		darken_member_info($('div .member_info:first'));
		return false;
	}
	
	/**
	 * member_info 박스를 클릭했을 때 해당 사용자의 정보가 txt_member와 member_srl(hidden)에 적용이 되어야 한다.
	 */
	function apply_member_info(event) {
		var member_srl = $(this).children('.member_srl').val();
		var user_name = $(this).children('.user_name').text();
		var nick_name = $(this).children('.nick_name').text();
		var user_id = $(this).children('.user_id').text();
		
		$('#hidden_member_srl').val(member_srl);
		$('#txt_member').val(nick_name);
		
		$('.member_info').remove();
	}
	/**
	 * 마우스가 들어왔을 때 색깔 찐하게 해주고 현재 선택된 녀석을 알리는 전역변수에 표시해주기
	 */
	function member_info_mouse_enter(event) {
		lighten_member_info($('div .member_info'));
		darken_member_info($(this));
		selected_member_info = parseInt($(this).attr('index'));
		event.stopPropagation();
	}
	/**
	 * 마우스가 떠날 때 색깔을 원래대로 돌려주고 현재 선택된 녀석을 알리느 전역변수에 표시해주기
	 */
	function member_info_mouse_leave(event) {
		lighten_member_info($(this));
		selected_member_info = 0;
		darken_member_info($('div .member_info:first'));
		event.stopPropagation();
	}
	
	/**
	 * 오브젝트를 받아서 진하게 칠한다. jQuery DOM 오브젝트여야 한다.
	 */
	function darken_member_info(object) {
		object.css('background-color', '#F3E2A9');
		object.css('border-color', '#F7BE81');
	}
	
	function lighten_member_info(object) {
		object.css('background-color', '#FBFBEF');
		object.css('border-color', '#F3E2A9');
	}
});