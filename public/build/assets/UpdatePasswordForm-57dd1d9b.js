import{r as p,v as f,c,d as o,a as e,u as r,w as m,L as v,g as y,o as _,b as V,p as g}from"./app-ddd1e4e5.js";import{_ as n}from"./InputError-e7727c9c.js";import{_ as d}from"./InputLabel-a336c410.js";import{P}from"./PrimaryButton-27973225.js";import{_ as i}from"./TextInput-3aa54041.js";import"./_plugin-vue_export-helper-c27b6911.js";const b={class:"col-lg-6 p-3"},S=o("header",null,[o("h2",null,"Update Password"),o("p",{class:"text-muted"}," Ensure your account is using a long, random password to stay secure. ")],-1),h=["onSubmit"],x={class:"mb-3"},k={class:"mb-3"},N={class:"mb-3"},U={key:0,class:"ms-2 text-success"},M={__name:"UpdatePasswordForm",setup(B){const l=p(null),u=p(null),s=f({current_password:"",password:"",password_confirmation:""}),w=()=>{s.put(route("password.update"),{preserveScroll:!0,onSuccess:()=>s.reset(),onError:()=>{s.errors.password&&(s.reset("password","password_confirmation"),l.value.focus()),s.errors.current_password&&(s.reset("current_password"),u.value.focus())}})};return(C,a)=>(_(),c("section",b,[S,o("form",{onSubmit:y(w,["prevent"]),class:"mt-6 space-y-6"},[o("div",x,[e(d,{for:"current_password",value:"Current Password"}),e(i,{id:"current_password",ref_key:"currentPasswordInput",ref:u,modelValue:r(s).current_password,"onUpdate:modelValue":a[0]||(a[0]=t=>r(s).current_password=t),invalid:r(s).errors.current_password,type:"password"},null,8,["modelValue","invalid"]),e(n,{message:r(s).errors.current_password},null,8,["message"])]),o("div",k,[e(d,{for:"password",value:"New Password"}),e(i,{id:"password",ref_key:"passwordInput",ref:l,modelValue:r(s).password,"onUpdate:modelValue":a[1]||(a[1]=t=>r(s).password=t),invalid:r(s).errors.password,type:"password"},null,8,["modelValue","invalid"]),e(n,{message:r(s).errors.password},null,8,["message"])]),o("div",N,[e(d,{for:"password_confirmation",value:"Confirm Password"}),e(i,{id:"password_confirmation",modelValue:r(s).password_confirmation,"onUpdate:modelValue":a[2]||(a[2]=t=>r(s).password_confirmation=t),invalid:r(s).errors.password_confirmation,type:"password"},null,8,["modelValue","invalid"]),e(n,{message:r(s).errors.password_confirmation},null,8,["message"])]),o("div",null,[e(P,{type:"submit",disabled:r(s).processing},{default:m(()=>[V("Save")]),_:1},8,["disabled"]),e(v,{"enter-active-class":"fade transition ease-in-out duration-500","enter-from-class":"opacity-0","enter-to-class":"opacity-100","leave-active-class":"fade transition ease-in-out duration-500","leave-to-class":"opacity-0"},{default:m(()=>[r(s).recentlySuccessful?(_(),c("span",U,"Saved.")):g("",!0)]),_:1})])],40,h)]))}};export{M as default};
