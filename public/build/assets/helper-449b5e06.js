function i(t){if(t)return new Date(t).toLocaleString()}function n(t,e=!0){if(t==0)return"0";let u=Math.floor(t/3600),f=Math.floor(t/60%60);t=t%60;let r="";return u>0&&(r+=u+" hours ",e)||f>0&&(r+=f+" minutes ",e)||t>0&&(r+=t+" seconds",e),r}export{i as f,n as g};
