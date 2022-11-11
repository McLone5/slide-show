FROM node:19-alpine

COPY entrypoint.sh /entrypoint
RUN ["chmod", "+x", "/entrypoint"]
ENTRYPOINT ["/entrypoint"]

RUN mkdir -p /.npm && chown -R 1000:1000 /.npm
USER 1000
